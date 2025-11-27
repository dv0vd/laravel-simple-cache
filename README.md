# @dv0vd/vuepress-auto-description

Automatically generate meta description for VuePress 2 pages from your Markdown content.

## Features

- Auto-generate meta description from first paragraph(s)
- Paragraph-safe trimming to a configurable maximum length

## Installation

```bash
npm install @dv0vd/vuepress-auto-description
```

## How It Works
1. **Check existing description**: If `page.frontmatter.description` is already set, the plugin does nothing and preserves the existing description.
2. **Extract paragraphs**: The plugin parses the rendered HTML (`page.contentRendered`) and extracts all `<p>` elements.
3. **Plain text**: HTML tags are removed, and whitespace is normalized. Empty paragraphs are ignored.
4. **Trim to max length**:
    - The plugin does not cut text in the middle of a word.
    - Paragraphs are added sequentially until the total length reaches `maxDescriptionLength`.
    - Because whole paragraphs are added, the final length **may slightly exceed the limit** to avoid cutting words in half.
5. **Save description**: The final text is assigned to `page.frontmatter.description`.

## Usage
```js
import autoDescriptionPlugin from '@dv0vd/vuepress-auto-description';

export default {
  plugins: [
    autoDescriptionPlugin(160),
  ],
};
```

## Options

| Option                    | Type   | Default | Description                                                                                           |
| ------------------------- | ------ | ------- | ----------------------------------------------------------------------------------------------------- |
| `maxDescriptionLength`    | number | 150     | Maximum number of characters for the description                                                    |
