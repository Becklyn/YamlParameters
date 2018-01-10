Yaml Parameters Handler
=======================

Automates the installation workflow for `parameters.yaml` files.


Installation and Usage
----------------------

Add the following code to your `composer.json`:


```json
{
    "require": {
        "becklyn/yaml-parameters-handler": "^1.0"
    },
    "scripts": {
        "yaml-parameters": [
            "Becklyn\\YamlParameters\\Script::handle"
        ],
        "post-install-cmd": [
            "@yaml-parameters"
        ],
        "post-update-cmd": [
            "@yaml-parameters"
        ]
    },
    "extra": {
        "parameters": "config/parameters.yaml"
    }
}
```

The `"extra"`-parameter is optional, it defaults to `config/parameters.yaml`.
The `.dist` file must be named like the target file, with additional `.dist` infix before the extension (the default is `config/parameters.yaml -> config/parameters.dist.yaml`)

*   All obsolete config will be removed without warning.
*   The merging of `.dist` parameters only works for scalars and arrays.
