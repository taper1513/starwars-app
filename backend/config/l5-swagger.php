<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Star Wars API',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    base_path('app/Http/Controllers'),
                ],
                'docs' => storage_path('api-docs'),
                'excludes' => [],
                'base' => base_path(),
            ],

        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],
        'paths' => [
            'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
            'docs_json' => 'api-docs.json',
            'docs_yaml' => 'api-docs.yaml',
            'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
            'annotations' => [
                base_path('app/Http/Controllers'),
            ],
            'docs' => storage_path('api-docs'),
            'excludes' => [],
            'base' => base_path(),
        ],
        'scanOptions' => [
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,
            'exclude' => [],
        ],
        'securityDefinitions' => [
            'securitySchemes' => [],
            'security' => [],
        ],
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy' => env('L5_SWAGGER_PROXY', false),
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url' => null,
        'ui' => [
            'display_doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
            'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),
            'display_operation_id' => env('L5_SWAGGER_UI_DISPLAY_OPERATION_ID', false),
            'default_models_expand_depth' => env('L5_SWAGGER_UI_DEFAULT_MODELS_EXPAND_DEPTH', 1),
            'default_model_expand_depth' => env('L5_SWAGGER_UI_DEFAULT_MODEL_EXPAND_DEPTH', 1),
            'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            'show_extensions' => env('L5_SWAGGER_SHOW_EXTENSIONS', true),
            'show_common_extensions' => env('L5_SWAGGER_SHOW_COMMON_EXTENSIONS', true),
            'deep_linking' => env('L5_SWAGGER_UI_DEEP_LINKING', true),
            'display_request_duration' => env('L5_SWAGGER_UI_DISPLAY_REQUEST_DURATION', true),
            'try_it_out_enabled' => env('L5_SWAGGER_UI_TRY_IT_OUT_ENABLED', true),
            'syntax_highlight' => [
                'activate' => env('L5_SWAGGER_UI_SYNTAX_HIGHLIGHT_ACTIVATE', true),
                'theme' => env('L5_SWAGGER_UI_SYNTAX_HIGHLIGHT_THEME', 'agate'),
            ],
            'mutator' => env('L5_SWAGGER_UI_MUTATOR', null),
        ],
    ],
]; 