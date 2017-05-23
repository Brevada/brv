<?php
/**
 * View
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\views;

/**
 * The View represents the final content that is sent to the client,
 * including HTTP headers, the content of the request itself, and any
 * necessary rendering information.
 */
class View
{
    /**
     * Default View options which can be overridden in the constructor.
     *
     * buffered: Flag indicates the presence of output buffering.
     * type: Indicates the nature of the content, which then controls Content-Type.
     * headers: Flag indicates if headers should be sent ('no headers' is useful for embedding).
     * code: The HTTP status code. If false, it will assume.
     * params: Parameters passed to the templating engine.
     * matches: Pattern matches from route rule.
     *
     * @var array
     */
    private $opts = [
        'buffered' => true,
        'type' => false,
        'headers' => [],
        'code' => false,
        'params' => [],
        'matches' => []
    ];

    /** @var string Data field required for the "rendering engine". */
    protected $data;

    /** @var \Twig_Environment Single Twig instance. */
    public static $twig;

    /**
     * A view is instantiated with a data field for the rendering engine
     * and optional options.
     *
     * @param mixed $data Data field.
     * @param array $opts Options.
     */
    public function __construct($data, $opts = [])
    {
        $this->data = $data;

        /* Hydrate view options. */
        $this->opts = array_merge($this->opts, $opts);

        /* Infer data type if not explicit. */
        if ($this->opts['type'] === false) {
            if (is_array($this->data)) {
                $this->opts['type'] = 'json';
            } elseif (is_string($this->data)) {
                $this->opts['type'] = 'twig';
            }
        }
    }

    /**
     * Retrieve or instantiate a Twig singleton.
     *
     * @return \Twig_Environment
     */
    public static function getTwig()
    {
        if (isset(self::$twig)) {
            return self::$twig;
        }

        $loader = new \Twig_Loader_Filesystem(NAMESPACE_IMPL_DIR . 'views');

        $twig_opts = [
            'debug' => DEBUG
        ];

        if (!DEBUG) {
            /* Do not cache when in DEBUG environment. */
            $twig_opts['cache'] = NAMESPACE_IMPL_DIR . 'views_cache';
        }

        self::$twig = new \Twig_Environment($loader, $twig_opts);
        self::$twig->addExtension(new twig\Asset());
        self::$twig->addExtension(new \nochso\HtmlCompressTwig\Extension());

        return self::$twig;
    }

    /**
     * Writes the rendered content according to type.
     */
    private function inc()
    {
        switch ($this->opts['type']) {
            case 'twig':
                $params = array_merge([
                    'meta' => [
                        'root' => brv_url(),
                        'canonical' => brv_url() . $this->opts['matches'][0]
                    ]
                ], $this->opts['params']);
                echo self::getTwig()->render($this->data . '.twig', $params);
                break;
            case 'json':
                echo json_encode($this->data);
                break;
            case 'download':
                readfile($this->data['path']);
                break;
            default:
                break;
        }
    }

    /**
     * Sends any necessary headers to the client.
     */
    private function sendHeaders()
    {
        if ($this->opts['code'] !== false) {
            http_response_code($this->opts['code']);
        }

        /* Send custom view specific headers. */
        if ($this->opts['headers'] !== false && is_array($this->opts['headers'])) {
            foreach ($this->opts['headers'] as $headr) {
                header(trim($headr));
            }
        }

        switch ($this->opts['type']) {
            case 'html':
            case 'twig':
                break;
            case 'json':
                header('Content-type: application/json;charset=utf-8');
                break;
            case 'external':
                \App::redirect(preg_replace_callback('|(\[([0-9]+)\])|', function($matches) {
                    if (isset($this->opts['matches'][intval($matches[2])])) {
                        return $this->opts['matches'][intval($matches[2])];
                    }
                }, $this->data), true);
                break;
            default:
                break;
        }
    }

    /**
     * Renders the view, writing content and sending necessary headers.
     */
    public function render()
    {
        if ($this->opts['headers'] !== false) {
            $this->sendHeaders();
        }

        if ($this->opts['buffered']) {
            ob_start();
            $this->inc();
            $contents = ob_get_contents();
            ob_end_clean();
            echo $contents;
        } else {
            $this->inc();
        }
    }
}
