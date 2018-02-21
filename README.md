# Heise Shariff for Magento 2

The module integrates the awesome privacy-aware social sharing
buttons solution into your Magento 2 project.

## Installation

This module needs a bunch of libraries and thus is only available through composer.

```bash
composer require dreipunktnull/magento2-module-shariff
bin/magento module:enable Dreipunktnull_Shariff
bin/magento setup:upgrade

```

## Usage

The module exposes the `Dreipunktnull\Shariff\Block\Shariff` block.

It can be mounted anywhere:

```xml
<referenceContainer name="product.info.main">
    <block class="Dreipunktnull\Shariff\Block\Shariff" name="dpn.shariff.links" after="product.info.price" template="Dreipunktnull_Shariff::shariff.phtml"/>
</referenceContainer>
```

## License

MIT
