<?php

namespace AppBundle\Libraries\JBBCode\visitors;

/**
 * This visitor is an example of how to implement smiley parsing on the JBBCode
 * parse graph. It converts :) into image tags pointing to /smiley.png.
 *
 * @author jbowens
 * @since April 2013
 */
class SmileyVisitor implements \AppBundle\Libraries\JBBCode\NodeVisitor
{

    function visitDocumentElement(\AppBundle\Libraries\JBBCode\DocumentElement $documentElement)
    {
        foreach($documentElement->getChildren() as $child) {
            $child->accept($this);
        }
    }

    function visitTextNode(\AppBundle\Libraries\JBBCode\TextNode $textNode)
    {
        /* Convert :) into an image tag. */
        $textNode->setValue(str_replace(':)', 
                                        '<img src="/smiley.png" alt=":)" />', 
                                        $textNode->getValue()));
    }

    function visitElementNode(\AppBundle\Libraries\JBBCode\ElementNode $elementNode)
    {
        /* We only want to visit text nodes within elements if the element's
         * code definition allows for its content to be parsed.
         */
        if ($elementNode->getCodeDefinition()->parseContent()) {
            foreach ($elementNode->getChildren() as $child) {
                $child->accept($this);
            }
        }
    }

}
