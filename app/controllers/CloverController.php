<?php


class CloverController extends BaseController
{

    public function coverage()
    {
        $file = app_path('..') . DIRECTORY_SEPARATOR . 'build/logs/clover.xml';
        if (!file_exists($file)) {
            return View::make('error.general')->with('message', 'No clover file.');
        }
        $data = simplexml_load_file($file);
        $info = [];

        foreach ($data->project->children() as $file) {
            foreach ($file->children() as $class) {
                if ($class->getName() == 'class') {
                    $attributes = $class->attributes();
                    $children = $class->children();
                    $metrics = $children->metrics;
                    $name = $attributes->name->__toString();

                    // some metrics:
                    $methods = intval($metrics->attributes()->methods->__toString());
                    $coveredmethods = intval($metrics->attributes()->coveredmethods->__toString());
                    $statements = intval($metrics->attributes()->statements->__toString());
                    $coveredstatements = intval($metrics->attributes()->coveredstatements->__toString());
                    $elements = intval($metrics->attributes()->elements->__toString());
                    $coveredelements = intval($metrics->attributes()->coveredelements->__toString());

                    $mPCT = $coveredmethods == 0 ? 0 : round(($coveredmethods / $methods) * 100);
                    $sPCT = $coveredstatements == 0 ? 0 : round(($coveredstatements / $statements) * 100);
                    $ePCT = $coveredelements == 0 ? 0 : round(($coveredelements / $elements) * 100);


                    $info[$name] = [
                        'methods'    => $methods > 0 ? $mPCT : null,
                        'statements' => $statements > 0 ? $sPCT : null,
                        'elements'   => $elements > 0 ? $ePCT : null,
                    ];

                }
            }
        }

        return View::make('clover.clover')->with('info', $info);
    }

    public function coverClass($class)
    {
        $file = app_path('..') . DIRECTORY_SEPARATOR . 'build/logs/clover.xml';
        if (!file_exists($file)) {
            return View::make('error.general')->with('message', 'No clover file.');
        }
        $data = simplexml_load_file($file);
        $return = [];

        $node = $data->xpath('//file[contains(@name,"' . $class . '")]');
        if (count($node) == 1) {
            $fileNode = $node[0];
            $attributes = $fileNode->attributes();
            $fileName = $attributes->name;
            $fileContent = file_get_contents($fileName->__toString());
            $fileLines = explode("\n", $fileContent);

            $covered = [];

            foreach ($fileNode->children() as $child) {
                if ($child->getName() == 'line') {
                    $lineNumber = intval($child->attributes()->num->__toString());
                    $count = intval($child->attributes()->count->__toString());
                    $covered[$lineNumber] = ($count > 0);
                }
            }

            // loop file lines
            foreach ($fileLines as $index => $line) {
                $lineNumber = $index + 1;
                $isCovered = isset($covered[$lineNumber]) ? $covered[$lineNumber] : null;
                $return[$index] = [
                    'line'    => $line,
                    'covered' => $isCovered
                ];
            }
            return View::make('clover.class')->with('return', $return);

        }







    }
}