<?php
use Leobard\KirbyLinkedData\LinkedDataForPage;

$linkedDataForPage = new LinkedDataForPage($page);
$jsonLD = $linkedDataForPage->generateJsonLD();
if (size($jsonLD) == 0)
    return;
?>
<script type="application/ld+json">
<?= json_encode(
    $jsonLD,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
); ?>

</script>
