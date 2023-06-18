<?php

namespace App\Extensions\CareCloudApi;

use App\Extensions\CareCloudApi\Concerns\Endpoints;
use GuzzleHttp\Client as GuzzleClient;
use Exception;

use function get_site_transient;
use function set_site_transient;

class Client
{
    use Endpoints;

    protected GuzzleClient $client;
    protected string $token;
    protected string $tokenTransientName = 'carecloud_api_token';
    protected string $baseUri = 'xxxxx';

    public function __construct()
    {}

    public static function init(): self
    {
        $instance = new self();
        $instance->setBearerToken();
        $instance->client = new GuzzleClient([
            'base_uri' => $instance->baseUri,
            'headers' => [
                'Authorization' => "Bearer {$instance->token}",
            ]
        ]);

        return $instance;
    }

    private function setBearerToken(): void
    {
        $this->token = $token = get_site_transient($this->tokenTransientName);

        if( ! $token ) {
            $res = (new GuzzleClient())
                ->post("{$this->baseUri}users/actions/login", [
                    'form_params' => [
                        'login' => CARECLOUD_API_USER,
                        'password' => CARECLOUD_API_PASSWORD,
                        'user_external_application_id' => CARECLOUD_API_APP_ID,
                    ]
                ]);

            $data = json_decode($res->getBody()->getContents(), false)->data;

            $this->token = $data->bearer_token;
            set_site_transient(
                $this->tokenTransientName,
                $this->token,
                HOUR_IN_SECONDS,
            );
        }
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        } elseif (method_exists($this->client, $name)) {
            return $this->client->{$name}(...$arguments);
        }

        throw new Exception("Property or method {$name} does not exist.");
    }
}
