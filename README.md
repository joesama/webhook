# WebHook
HTTP Request Wrapper 

![Webhook Installation Validation](https://github.com/joesama/webhook/workflows/Webhook%20Installation%20Validation/badge.svg) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/joesama/webhook/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/joesama/webhook/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/joesama/webhook/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/joesama/webhook/?branch=master) 
[![Build](https://scrutinizer-ci.com/g/joesama/webhook/badges/build.png?b=master)](https://scrutinizer-ci.com/g/joesama/webhook/badges/build.png?b=master) 
![PHPStan](https://github.com/joesama/webhook/workflows/PHPStan/badge.svg)

# Installation

Simple installation via composer :

```bash
composer require "joesama/webhook"
```

## Usage/Examples

### Define connector 

If in need of pre-configured connector, a connector class can be created by extending ```Joesama\Webhook\Connectors\AbstractConnector``` class and implement
```Joesama\Webhook\Connectors\ConnectorContract``` interface.

```
    class ExamplesConnector extends AbstractConnector implements ConnectorContract
    {
        /**
         * Define configuration parameter to be attach to request.
         */
        public function webHookConfiguration(): array
        {
            return [];
        }
    
        /**
         * Define request content to be send.
         */
        public function webHookContent(): array
        {
            return [];
        }
    
        /**
         * Define request header to attach to request.
         */
        public function webHookHeader(): array
        {
            return [];
        }
    }
```

### Initiate Request

To initiate a request can be simply by calling ```Joesama\Webhook\Web\Hook``` class and called the ```getResponse``` method.

```
    $hook = new Hook();
    
    $hook->setRequestBody([]);
    
    $hook->getResponse('http://www.google.com', 'GET');
```

To use pre-configured connector, pass it as parameter to the ```Hook``` class

```
    $connector = new ExamplesConnector();

    $hook = new Hook($connector);
```


# License

joesama/webhook is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

