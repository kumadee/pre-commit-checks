<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\Graph;
use PhpDA\Writer\Extractor\ExtractionInterface;
use PhpDA\Writer\Extractor\Graph as GraphExtractor;

class SVG_TXT_CLASS extends AbstractGraphViz implements StrategyInterface
{

    /** @var ExtractionInterface */
    private $extractor;

    /**
     * @param ExtractionInterface $extractor
     */
    public function setExtractor(ExtractionInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @return ExtractionInterface
     */
    public function getExtractor()
    {
        if (!$this->extractor instanceof ExtractionInterface) {
            $this->extractor = new GraphExtractor;
        }

        return $this->extractor;
    }

    /**
     * @param Graph $graph
     * @return string
     */
    public function toString(Graph $graph)
    {
        $output = $this->getExtractor()->extract($graph);

        //$file = fopen("/app/dependencies_CLASS.txt", "w") or die("Unable to open file!");

        $txt = $this->checkCycles($output);
        $txt .= $this->checkHierarchy($output);
        //fwrite($file, $txt);

        //return $this->getGraphViz()->setFormat('svg')->createImageData($graph);
        return $txt;
    }

    private function checkCycles($data): string
    {
        $count = 1;
        $txt = "*Detected " . count($data['cycles']) . " cycle(s)!*\n\n";
        foreach ($data['cycles'] as $cycle) {
            $txt .= "Cycle " . $count++ . ":\n";
            foreach ($cycle as $className) {
                $txt .= "\t$className\n";
            }
            $txt .= "\n";
        }
        if ($data['cycles'] > 0) {
            echo $txt;
        }
        return $txt;
    }

    public function checkHierarchy($data): string
    {
        if (empty($data['edges'])) {
            throw new Exception("No Data found!");
        }

        $violations = $this->analyzeData($data);
        return $this->formatViolations($violations);
    }

    public function analyzeData(array $data): array
    {
        $violations = [];
        foreach ($data['edges'] as $key => $edge) {
            if ($this->edgeViolatesHierarchy($edge)) {
                $violations[] = $edge;
            }
        }
        return $violations;
    }

    private function edgeViolatesHierarchy(array $edge): bool
    {
        $fromHierarchy = $this->getHierarchyLevel($edge['from']);
        if (empty($fromHierarchy) && $fromHierarchy !== 0) {
            return false;
        }

        $toHierarchy = $this->getHierarchyLevel($edge['to']);
        if (empty($toHierarchy)) {
            return false;
        }

        return $fromHierarchy < $toHierarchy;
    }

    public function formatViolations(array $violations): string
    {
        $txt = '';

        if (empty($violations)){
            $txt = "*Detected 0 hierarchy violation(s)!*";
        }
        else {
            foreach ($violations as $violation) {
                $txt .= "*Hierarchy violation detected!*\n";
                $txt .= "From: " . $violation['from'] . "\nTo: " . $violation['to'] . "\n\n";
            }
            echo $txt;
        }

        return $txt;
    }

    /**
     * @param string $classPath
     * @return int|null
     */
    private function getHierarchyLevel(string $classPath)
    {
        $hierarchy = ['\\Mapper\\', '\\Dao\\', '\\Service\\', '\\Facade\\', '\\Bridge\\'];
        for ($i = 0; $i < count($hierarchy); $i++) {
                if (strpos($classPath, $hierarchy[$i]) !== false) {
                    return $i;
                }
            }
        return null;
    }
}
