<?php

namespace Browscap\Generator;

use DOMDocument;
use DOMNode;

class BrowscapXmlGenerator extends AbstractGenerator
{
    /**
     * Generate and return the formatted browscap data
     *
     * @return string
     */
    public function generate()
    {
        return $this->render(
            $this->collectionData,
            array_keys(array('Parent' => '') + $this->collectionData['DefaultProperties'])
        );
    }

    /**
     * Generate the header
     *
     * @param \DOMDocument $dom
     *
     * @return \DOMElement
     */
    private function renderHeader(DOMDocument $dom)
    {
        $comments = $dom->createElement('comments');

        foreach ($this->getComments() as $text) {
            $comment = $dom->createElement('comment');
            $cdata   = $dom->createCDATASection($text);
            $comment->appendChild($cdata);
            $comments->appendChild($comment);
        }

        return $comments;
    }

    /**
     * renders all found useragents into a string
     *
     * @param array[] $allDivisions
     * @param array   $allProperties
     *
     * @return string
     */
    private function render(array $allDivisions, array $allProperties)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $xmlRoot   = $dom->createElement('browsercaps');
        $xmlRoot->appendChild($this->renderHeader($dom));
        $xmlRoot->appendChild($this->renderVersion($dom));

        $items = $dom->createElement('browsercapitems');

        $counter = 1;

        foreach ($allDivisions as $key => $properties) {
            $counter++;

            if (!$this->firstCheckProperty($key, $properties, $allDivisions)) {
                continue;
            }

            // create output - xml
            $browscapitem = $dom->createElement('browscapitem');
            $name = $dom->createAttribute('name');
            $name->value = htmlentities($key);
            $browscapitem->appendChild($name);

            $this->createItem($dom, $browscapitem, 'PropertyName', $key);
            $this->createItem($dom, $browscapitem, 'AgentID', $counter);
            $this->createItem($dom, $browscapitem, 'MasterParent', $this->detectMasterParent($key, $properties));

            $valueOutput = ((!isset($properties['lite']) || !$properties['lite']) ? 'false' : 'true');
            $this->createItem($dom, $browscapitem, 'LiteMode', $valueOutput);

            foreach ($allProperties as $property) {
                if (!CollectionParser::isOutputProperty($property)) {
                    continue;
                }

                $this->createItem($dom, $browscapitem, $property, $this->formatValue($property, $properties));
            }

            $items->appendChild($browscapitem);
        }

        $xmlRoot->appendChild($items);
        $dom->appendChild($xmlRoot);

        return str_replace('  ', '', $dom->saveXML());
    }

    /**
     * renders the version information
     *
     * @param \DOMDocument $dom
     *
     * @return \DOMElement
     */
    private function renderVersion(DOMDocument $dom)
    {
        $version     = $dom->createElement('gjk_browscap_version');
        $versionData = $this->getVersionData();

        if (!isset($versionData['version'])) {
            $versionData['version'] = '0';
        }

        if (!isset($versionData['released'])) {
            $versionData['released'] = '';
        }

        $item = $dom->createElement('item');
        $name = $dom->createAttribute('name');
        $name->value = 'Version';
        $value = $dom->createAttribute('value');
        $value->value = $versionData['version'];
        $item->appendChild($name);
        $item->appendChild($value);
        $version->appendChild($item);

        $item = $dom->createElement('item');
        $name = $dom->createAttribute('name');
        $name->value = 'Released';
        $value = $dom->createAttribute('value');
        $value->value = $versionData['released'];
        $item->appendChild($name);
        $item->appendChild($value);
        $version->appendChild($item);

        return $version;
    }

    /**
     * @param \DOMDocument $dom
     * @param \DOMNode     $browscapitem
     * @param string       $property
     * @param mixed        $valueOutput
     */
    private function createItem(DOMDocument $dom, DOMNode $browscapitem, $property, $valueOutput)
    {
        $item        = $dom->createElement('item');

        $name        = $dom->createAttribute('name');
        $name->value = htmlentities($property);
        $item->appendChild($name);

        $value        = $dom->createAttribute('value');
        $value->value = htmlentities($valueOutput);
        $item->appendChild($value);

        $browscapitem->appendChild($item);
    }
}
