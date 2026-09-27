---
name: schemaorg-that-blueprint
description: Annotate a Kirby CMS blueprint YAML file with schema.org RDF mappings — a top-level `rdftype` and per-field `rdfproperty` keys — so JSON-LD can later be generated from the blueprint's own fields. Use when asked to "RDF" or "schema" a blueprint, to map blueprint fields to a schema.org type, or to invoke "rdf that blueprint".
---

# RDF that blueprint

Given one single Kirby blueprint file (a page, file, or user blueprint YAML) and a
target schema.org type, annotate the blueprint in place so its fields carry
enough metadata to later be serialized as JSON-LD, without touching how the
blueprint actually renders in the Panel.

## Inputs

- The blueprint file to annotate (e.g. `site/blueprints/pages/about.yml`).
- The target schema.org type. If the user names it (e.g. "map it to
  schema:Organization"), use that. If they don't, infer the most likely type
  from the blueprint's title/filename/fields and confirm with the user before
  writing anything, rather than guessing silently.

## Steps

1. **Read the blueprint file fully first.** Note its existing structure,
   comments, and indentation style — edits must be surgical (use a
   find/replace style edit tool), not a rewrite. Comments and formatting
   the user already has are part of their work; preserve them exactly.

2. **Know the target type's properties.** Recall schema.org's property list
   for the target type (e.g. https://schema.org/Organization) from what you
   already know for common types. If you're unsure whether a property exists
   or unsure of a less common type's full property list, fetch the
   `https://schema.org/<Type>` page rather than guessing.

3. **Add the RDFS:Class to be used as target type at the blueprint's top level:**

   ```yaml
   rdftype: schema:<Type>
   ```

   This goes as a sibling of top-level keys like `title`, `status`, `options`
   — not nested inside a tab, column, or field. Placing it near the top
   (e.g. after `options`) is a reasonable default if the user hasn't shown a
   preference.

4. **Walk every `fields:` block in the file** — top-level fields, fields
   inside `columns:`, and nested `fields:` inside structure/object fields.
   For each field definition, decide whether it plausibly represents one
   schema.org property of the target type. Base the judgment on the field's
   `name`, `label`, `type`, and `help` together 
   (e.g. a field named `address` of type `writer` → `schema:address`; a field named `phone` of type `tel` →
   `schema:telephone`).

5. **Annotate matches** by adding `rdfproperty` as a new key inside that
   field's own property block, alongside `label`/`type`/etc.:

   ```yaml
   address:
     label: Address
     type: writer
     inline: true
     rdfproperty: schema:address
   ```

6. **Structure fields (repeatable rows)**: leave fields with `type:structure`
    unannotated.

7. **Don't force weak matches.** If no field plausibly maps to a given
   schema.org property, or a field doesn't map to anything on the target
   type, leave it unannotated. A missing annotation is better than a wrong
   one. Purely presentational/layout fields (e.g. a modular `layout` field)
   never get an `rdfproperty`.

8. **One property per concept.** Don't annotate two unrelated fields with
   the same `rdfproperty` unless the schema.org property genuinely accepts
   multiple values (like `sameAs`) and the fields are each a source of one
   of those values.

9. **After editing, report back** a short table: field name → type →
   `rdfproperty` assigned, plus which fields were deliberately left
   unannotated and why. This makes the mapping easy to sanity-check without
   re-reading the whole diff.

## Out of scope

This skill only edits one blueprint YAML. It does not touch any PHP code.
