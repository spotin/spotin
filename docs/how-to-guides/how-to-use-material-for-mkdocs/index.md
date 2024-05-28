# How to use Material for MkDocs

## Installation

Install
[Material for MkDocs](../../explanations/about-material-for-mkdocs/index.md)
with the following commands.

=== ":simple-linux: Linux"

    ```sh
    TODO
    ```

=== ":simple-apple: macOS"

    ```sh
    # Install Material for MkDocs and all its extensions
    pip3 install \
    	cairosvg \
    	mkdocs-git-revision-date-localized-plugin \
    	mkdocs-glightbox \
    	mkdocs-material \
    	mkdocs-minify-plugin \
    	pillow

    # Install Material for MkDocs dependencies
    brew install \
    	cairo \
    	freetype \
    	libffi \
    	libjpeg \
    	libpng \
    	zlib
    ```

=== ":simple-windows: Windows"

    ```sh
    TODO
    ```

## Start the website for development

```sh
mkdocs serve
```

## Build the website for production

```sh
mkdocs build
```

## Configuration

The configuration for Material for MkDocs is located in the `mkdocs.yml`
configuration file.

## Common tasks

### Add a new page

Add a new page by creating a new file/directory in the `docs` directory. All
pages must have a `.md` file extension.

### Add a new navigation entry

Add a new entry to the navigation in the `mkdocs.yml` file under the `nav`
property.

### Add a new glossary entry

Add a new entry to the glossary in the `docs/glossary.md` file.

The format must be as follow.

```
*[Abbr]: The full definition of the abbreviation
```

Each word that Material for MkDocs will find in the documentation will have a
tooltip with the definition for the word.

## Related explanations

These explanations are related to the current item (in alphabetical order).

- [About Markdown](../../explanations/about-markdown/index.md)
- [About Material for MkDocs](../../explanations/about-material-for-mkdocs/index.md)

## Resources and alternatives

These resources and alternatives are related to the current item (in
alphabetical order).

- [Keys](https://facelessuser.github.io/pymdown-extensions/extensions/keys/)
