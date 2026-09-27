<?php

namespace Leobard\KirbyLinkedData;

use Kirby\Cms\Blueprint;
use Kirby\Cms\Page;
use Kirby\Content\Field;
use Kirby\Toolkit\Str;

/**
 * Renders JSON-LD structured data for a page, based on
 * `rdftype` (blueprint-level) and `rdfproperty` (field-level)
 * annotations added to the page's blueprint.
 */
class LinkedDataForPage
{

    /**
     * The page for which linked data should be created
     */
    public readonly Page $page;

    /**
     * The blueprint of the page
     */
    public readonly Blueprint $blueprint;

    function __construct(Page $page)
    {
        $this->page = $page;
        $this->blueprint = $page->blueprint();
    }

    /**
     * Builds a `<script type="application/ld+json">` tag for the
     * given page, or an empty string if its blueprint does not
     * define an `rdftype`.
     */
    public function generateJsonLD(): array
    {
        $type      = $this->blueprint->rdftype();

        if (empty($type) === true) {
            return [];
        }

        $data = [
            '@context' => 'https://schema.org',
            // the rdf:type of the resource
            '@type'    => Str::after($type, 'schema:'),
        ];
        // the URI of the rdfs:Resource that is going to be described by statements. See README
        $data['@id'] = $this->page->url();
        foreach ($this->blueprint->fields() as $name => $fieldBlueprint) {
            $property = $fieldBlueprint['rdfproperty'] ?? null;

            if ($property === null) {
                continue;
            }
            /**
             * @var \Kirby\Content\Field $field
             */
            $field = $this->page->content()->get($name);
            if (($field == null) || ($field->isEmpty())){
                continue;
            }
            $jsonldvalue = $this->fieldToJsonLDValue($field, $fieldBlueprint);
            $data[Str::after($property, 'schema:')] = $jsonldvalue;
        }
        return $data;
    }

    /**
     * get the toString of the $field.
     * Depending on the 'type' defined in the $fieldBlueprint, the following conversions are done:
     *
     * - writer: as this contains html, it is run through Str::unhtml.
     */
    function fieldToJsonLDValue(Field $field, array $fieldBlueprint) {
        $type = $fieldBlueprint['type'] ?? 'text';
        $toString = $field->toString();
        // kirby field type: writer contains hthml. by default, strip it for JSON-LD
        if ($type === 'writer') {
            // alternative: strip_tags(), but unhtml() is probably enough
            $toString = trim(Str::unhtml($toString));
        }
        return $toString;
    }


}
