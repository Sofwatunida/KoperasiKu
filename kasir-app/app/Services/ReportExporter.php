<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

/**
 * Membuat berkas laporan (PDF, Excel .xlsx, Word .docx) tanpa menambah
 * dependency baru. Format Excel dan Word memakai OOXML asli sehingga benar-benar
 * dapat dibuka oleh Excel/Word, sedangkan PDF ditulis sebagai berkas PDF valid.
 */
class ReportExporter
{
    private const PDF_LINE_HEIGHT = 14.0;

    private const PDF_MARGIN_X = 36.0;

    private const PDF_MARGIN_TOP = 806.0;

    private const PDF_LINES_PER_PAGE = 56;

    /**
     * @param  array{
     *     title: string,
     *     period: array{dari: string, sampai: string},
     *     printed_at: string,
     *     columns: array<int, string>,
     *     rows: array<int, array<int, string|int>>,
     *     summary: array<int, array{0: string, 1: string|int}>
     * }  $report
     */
    public function pdf(array $report): string
    {
        $lines = $this->pdfLines($report);
        $pages = array_chunk($lines, self::PDF_LINES_PER_PAGE) ?: [[]];

        $objects = [];
        $kids = [];
        $pageObjectNumber = 4;

        foreach ($pages as $page) {
            $contentObjectNumber = $pageObjectNumber + 1;
            $kids[] = "{$pageObjectNumber} 0 R";

            $objects[$pageObjectNumber] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842]'
                .' /Resources << /Font << /F1 3 0 R >> >>'
                ." /Contents {$contentObjectNumber} 0 R >>";

            $stream = $this->pdfContentStream($page);
            $objects[$contentObjectNumber] = '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream";

            $pageObjectNumber += 2;
        }

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $kids).'] /Count '.count($pages).' >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>';

        ksort($objects);

        return $this->assemblePdf($objects);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function xlsx(array $report): string
    {
        $sheet = $this->xlsxSheet($report);

        return $this->zip([
            '[Content_Types].xml' => $this->xlsxContentTypes(),
            '_rels/.rels' => $this->packageRels('xl/workbook.xml'),
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
                .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                .'</Relationships>',
            'xl/styles.xml' => $this->xlsxStyles(),
            'xl/worksheets/sheet1.xml' => $sheet,
        ]);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function docx(array $report): string
    {
        return $this->zip([
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
                .'<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
                .'</Types>',
            '_rels/.rels' => $this->packageRels('word/document.xml'),
            'word/_rels/document.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                .'</Relationships>',
            'word/styles.xml' => $this->docxStyles(),
            'word/document.xml' => $this->docxDocument($report),
        ]);
    }

    /**
     * Baris teks PDF dengan lebar kolom tetap (font Courier).
     *
     * @param  array<string, mixed>  $report
     * @return array<int, string>
     */
    private function pdfLines(array $report): array
    {
        $lines = [
            $this->padText('KOPERASIKU', 58, false, true),
            '',
            $this->padText($report['title'], 0, false, true),
            'Periode    : '.$report['period']['dari'].' s/d '.$report['period']['sampai'],
            'Dicetak    : '.$report['printed_at'],
            '',
            $this->separator(),
        ];

        $columns = $report['columns'];
        $widths = $this->pdfColumnWidths(count($columns));
        $lines[] = $this->joinColumns($columns, $widths, true);
        $lines[] = $this->separator();

        foreach ($report['rows'] as $row) {
            $lines[] = $this->joinColumns($row, $widths);
        }

        $lines[] = $this->separator();

        foreach ($report['summary'] as $summary) {
            $lines[] = $summary[0].' : '.$summary[1];
        }

        return $lines;
    }

    /**
     * @param  array<int, string|int|float>  $cells
     * @param  array<int, int>  $widths
     */
    private function joinColumns(array $cells, array $widths, bool $header = false): string
    {
        $parts = [];

        foreach ($widths as $index => $width) {
            $raw = $cells[$index] ?? '';
            $numeric = ! $header && (is_int($raw) || is_float($raw));
            $value = $numeric ? number_format((float) $raw, 0, ',', '.') : (string) $raw;

            if (mb_strlen($value) > $width) {
                $value = mb_substr($value, 0, $width);
            }

            $parts[] = $this->padText($value, $width, $numeric);
        }

        return implode(' ', $parts);
    }

    /**
     * Lebar kolom agar seluruh tabel muat pada halaman A4 (Courier 9pt).
     *
     * @return array<int, int>
     */
    private function pdfColumnWidths(int $columns): array
    {
        return match ($columns) {
            5 => [12, 18, 18, 22, 14],
            6 => [10, 16, 14, 20, 6, 14],
            7 => [16, 10, 12, 14, 3, 10, 11],
            default => array_fill(0, max($columns, 1), (int) floor(96 / max($columns, 1))),
        };
    }

    private function separator(): string
    {
        return str_repeat('-', 84);
    }

    private function padText(string $value, int $width, bool $right = false, bool $bold = false): string
    {
        if ($bold) {
            $value = mb_strtoupper($value);
        }

        if ($width <= 0) {
            return $value;
        }

        $padding = str_repeat(' ', max($width - mb_strlen($value), 0));

        return $right ? $padding.$value : $value.$padding;
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function pdfContentStream(array $lines): string
    {
        $stream = "BT\n/F1 9 Tf\n".self::PDF_LINE_HEIGHT." TL\n"
            .self::PDF_MARGIN_X.' '.self::PDF_MARGIN_TOP." Td\n";

        foreach ($lines as $line) {
            $stream .= '('.$this->escapePdfText($line).") Tj\nT*\n";
        }

        return $stream.'ET';
    }

    private function escapePdfText(string $text): string
    {
        $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    /**
     * Merangkai objek-objek PDF lengkap dengan tabel xref.
     *
     * @param  array<int, string>  $objects
     */
    private function assemblePdf(array $objects): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $number => $content) {
            $offsets[$number] = strlen($pdf);
            $pdf .= "{$number} 0 obj\n{$content}\nendobj\n";
        }

        $count = count($objects);
        $startXref = strlen($pdf);

        $pdf .= "xref\n0 ".($count + 1)."\n0000000000 65535 f \n";

        for ($number = 1; $number <= $count; $number++) {
            $pdf .= str_pad((string) ($offsets[$number] ?? 0), 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size ".($count + 1)." /Root 1 0 R >>\nstartxref\n{$startXref}\n%%EOF";

        return $pdf;
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function xlsxSheet(array $report): string
    {
        $rows = [];

        $rows[] = [['value' => $report['title'], 'style' => 2]];
        $rows[] = [['value' => 'Periode: '.$report['period']['dari'].' s/d '.$report['period']['sampai']]];
        $rows[] = [['value' => 'Dicetak: '.$report['printed_at']]];
        $rows[] = [['value' => '']];
        $rows[] = array_map(fn (string $column) => ['value' => $column, 'style' => 1], $report['columns']);

        foreach ($report['rows'] as $row) {
            $rows[] = array_map(fn ($cell) => ['value' => $cell], array_values($row));
        }

        $rows[] = [['value' => '']];

        foreach ($report['summary'] as $summary) {
            $rows[] = [['value' => $summary[0], 'style' => 1], ['value' => $summary[1]]];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rowIndex => $cells) {
            $xml .= '<row r="'.($rowIndex + 1).'">';

            foreach (array_values($cells) as $cellIndex => $cell) {
                $reference = $this->columnLetter($cellIndex).($rowIndex + 1);
                $value = $cell['value'];
                $style = $cell['style'] ?? 0;
                $styleAttribute = $style > 0 ? ' s="'.$style.'"' : '';

                if (is_int($value)) {
                    $xml .= '<c r="'.$reference.'"'.$styleAttribute.'><v>'.$value.'</v></c>';
                } else {
                    $xml .= '<c r="'.$reference.'"'.$styleAttribute.' t="inlineStr"><is><t xml:space="preserve">'
                        .$this->escapeXml((string) $value).'</t></is></c>';
                }
            }

            $xml .= '</row>';
        }

        return $xml.'</sheetData></worksheet>';
    }

    private function xlsxContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private function xlsxStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="3">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="14"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            .'<borders count="1"><border/></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="3">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'</cellXfs>'
            .'</styleSheet>';
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function docxDocument(array $report): string
    {
        $body = $this->docxParagraph($report['title'], true);
        $body .= $this->docxParagraph('Periode: '.$report['period']['dari'].' s/d '.$report['period']['sampai']);
        $body .= $this->docxParagraph('Dicetak: '.$report['printed_at']);

        $table = '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/>'
            .'<w:tblBorders>'
            .'<w:top w:val="single" w:sz="4" w:space="0" w:color="999999"/>'
            .'<w:left w:val="single" w:sz="4" w:space="0" w:color="999999"/>'
            .'<w:bottom w:val="single" w:sz="4" w:space="0" w:color="999999"/>'
            .'<w:right w:val="single" w:sz="4" w:space="0" w:color="999999"/>'
            .'<w:insideH w:val="single" w:sz="4" w:space="0" w:color="999999"/>'
            .'<w:insideV w:val="single" w:sz="4" w:space="0" w:color="999999"/>'
            .'</w:tblBorders></w:tblPr>';

        $table .= '<w:tr>'.implode('', array_map(fn (string $column) => $this->docxCell($column, true), $report['columns'])).'</w:tr>';

        foreach ($report['rows'] as $row) {
            $table .= '<w:tr>'.implode('', array_map(fn ($cell) => $this->docxCell($cell), array_values($row))).'</w:tr>';
        }

        $table .= '</w:tbl>';

        $body .= $table;

        foreach ($report['summary'] as $summary) {
            $body .= $this->docxParagraph($summary[0].': '.$summary[1], false, true);
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body>'.$body
            .'<w:sectPr><w:pgSz w:w="11906" w:h="16838"/>'
            .'<w:pgMar w:top="1134" w:right="1134" w:bottom="1134" w:left="1134"/></w:sectPr>'
            .'</w:body></w:document>';
    }

    private function docxParagraph(string $text, bool $heading = false, bool $spaced = false): string
    {
        $runProperties = $heading ? '<w:rPr><w:b/><w:sz w:val="32"/></w:rPr>' : '';
        $spacing = $spaced ? '<w:pPr><w:spacing w:before="200"/></w:pPr>' : '';

        return '<w:p>'.$spacing.'<w:r>'.$runProperties
            .'<w:t xml:space="preserve">'.$this->escapeXml($text).'</w:t></w:r></w:p>';
    }

    private function docxCell(string|int|float $value, bool $header = false): string
    {
        $text = is_int($value) || is_float($value)
            ? number_format((float) $value, 0, ',', '.')
            : (string) $value;
        $runProperties = $header ? '<w:rPr><w:b/></w:rPr>' : '';

        return '<w:tc><w:tcPr><w:tcW w:w="0" w:type="auto"/></w:tcPr>'
            .'<w:p><w:r>'.$runProperties.'<w:t xml:space="preserve">'.$this->escapeXml($text).'</w:t></w:r></w:p></w:tc>';
    }

    private function docxStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="20"/></w:rPr></w:rPrDefault></w:docDefaults>'
            .'</w:styles>';
    }

    private function packageRels(string $documentPath): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="'.$documentPath.'"/>'
            .'</Relationships>';
    }

    private function columnLetter(int $index): string
    {
        $letters = '';

        for ($number = $index; $number >= 0; $number = intdiv($number, 26) - 1) {
            $letters = chr(65 + ($number % 26)).$letters;
        }

        return $letters;
    }

    /**
     * @param  array<string, string>  $files
     */
    private function zip(array $files): string
    {
        $temporary = tempnam(sys_get_temp_dir(), 'koperasiku-export-');

        if ($temporary === false) {
            throw new RuntimeException('Tidak dapat membuat berkas sementara untuk export.');
        }

        $archive = new ZipArchive;

        if ($archive->open($temporary, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak dapat membuat arsip export.');
        }

        foreach ($files as $name => $contents) {
            $archive->addFromString($name, $contents);
        }

        $archive->close();

        $binary = file_get_contents($temporary);
        @unlink($temporary);

        if ($binary === false) {
            throw new RuntimeException('Tidak dapat membaca hasil export.');
        }

        return $binary;
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
