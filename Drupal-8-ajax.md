Ajax (Asynchronous Javascript and XML — «асинхронный JavaScript и XML») - это процесс динамического обновления частей HTML-страницы на основе данных с сервера. Со стороны клиента (веб-браузера) выполняется запрос на сервер с помощью XMLHttpRequest, сервер в свою очередь выполняет необходимые действия и возвращает ответ в виде заголовков и данных в формате JSON или XML (так же можно передвать другие типы данных, к примеру HTML или текст, но указанные 2 формата наиболее популярны), далее скрипт, который инициализировал запрос распоряжается полученными данными по предварительно заданному алгоритму.

Но наша цель не изучение самой технологии AJAX, а его практическое применение в CMS Drupal 8 (далее Drupal). В связи с этим рекомендуется перед началом изучения статьи, ознакомиться c AJAX подходом.

В "ядре" Drupal реализован инструментарий для работы с наиболее часто встречающимися AJAX командами а так же предусмотрена возможность создания своих команд, для решения конкретной задачи.
Общий алгоритм взаимодействия следующий:

1. Создается гиперссылка с классом "use-ajax"
2. По клику на ссылку с указанным классом, скрипт (core/misc/ajax.js) выполняет асинхронный запрос на сервер, по указанному в аттрибуте HREF гиперссылки пути.
3. На стороне сервера, по пути, на которую ссылается гиперссылка, вызывается контролер, который выполняет все необходимые действия, генерирует список команд и возвращает формате JSON.
4. Скрипт получает набор команд и поочередно выполняет их.

А теперь подробнее разберем выше описанный процесс на примере отображения сообщения по клику на ссылку.


### Часть 1: Создание гиперссылок и автоматическая отправка запроса.

Гиперссылки вы можете создать любым удобным способом (добавив ссылки из текстового редактора, описав в файле шаблона TWIG или создав программный блок с текстом...). Важны 2 фактора:
1. наличи аттрибута href, где указан URL, который должен обработать AJAX запрос;
2. класс use-ajax, который является "индикатором" того, что клик по данной ссылке должен быть обработан скриптом.

Но есть один ньюанс, для корректной обработки AJAX ссылок, необходимо, чтобы на странице, где выводится данная ссылка, была подключена библиотека core/drupal.ajax (core/misc/ajax.js). Для решения этой задачи есть несколько способов.

1. Если ссылка выводится с помощью программного кода, то можно в "render массиве" указать следующее:

```
$output['#attached']['library'][] = 'core/drupal.ajax';
```

2. Если ссылка выводится в TWIG шаблоне, то можно использовать следующую конструкцию
```
{{ attach_library('core/drupal.ajax') }}
```


3. Есть более универсиальный вариант, подключить библиотеку в хуке hook_page_alter (файл mymodule.module):
```
function mymodule_page_alter(&$page) {
  if(***ATTACH CONDITIONS***){
    $page['#attached']['library'][] = 'core/drupal.ajax';
  }
}
```
*Где \*\*\*ATTACH CONDITIONS\*\*\* - условия, по которой определяете когда необходимо подключать AJAX библиотеку.*


Или любым другим известным вам вариантом подключения библиотеки к странице.

После подключения библиотеки, все ссылки с классом use-ajax будут выполнять асинхронные(AJAX) запросы.

### Часть 2: Обработка запроса.

Как уже было ранее сказано, после того, как произошло событие клика на ссылку, выполняется запрос по URL, который указан в аттрибуте href. Для обработки запроса нам нужно создать контроллер, который будет вызываться по обращению на указанный URL.

Для начала "расскажем" Drupal-у о том, что по указанной ссылке.
Создаем модуль: для примера допустим, что наш модуль называется cusom_ajax, контролер обработчика будет доступен по адресу /ajax/show-message.

Создаем папку custom_ajax в modules/custom/.
Добавляем файл опсиания custom_ajax.info.yml

```
name: Custom ajax
description: 'My first module'
type: module
core: 8.x
```

Теперь описываем новую страницу с помощью файла custom_ajax.routing.yml

```
custom_ajax.ajax:
  path: '/ajax/show-message'
  defaults:
    _controller: '\Drupal\cusom_ajax\Controller\CustomAjaxController::ajax_show_message'
  requirements:
    _permission: 'access content'
```

Этим файлом "указали" Drupal-у следующую инструкцию: при обращении по пути /ajax/show-message необходимо вывзвать метод ajax_show_message который находится в классе CustomAjaxController. А так же указали путь, где находится указанный класс (в нашем случае это /modules/custom/custom_ajax/src/Controller/CustomAjaxController.php).

```
// Определяем область видимости нашего класса.
namespace Drupal\cusom_ajax\Controller;

// Импортируем зависимости.
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;

/**
 * Определяем контролер CustomAjaxController.
 */
class CustomAjaxController extends ControllerBase {

  public function ajax_show_message() {
    // Создаем экземпляр класса AjaxResponse().
    $response = new AjaxResponse();

    // Добавляем команду js: alert().
    $response->addCommand(new AlertCommand('Hello world'));

    //Возвращаем набор команд для выполнения.
    return $response;
  }

}

```

Включаем модуль, выполняем клик на ссылку и получаем alert окно с текстом Hello World.

В примере мы использовали только команду AlertCommand(). В ядре Drupal-а есть набор предустановленных команд. вот некоторые из них:

* AlertCommand - вызывает alert();
* RedirectCommand - выполняет ajax перенаправление;
* AddCssCommand - добавляет css на страницу;
* InvokeCommand - выполняет пользовательскую команду jQuery;
* CssCommand - выполняет команду jQuery.css();
* AfterCommand - выполняет команду jQuery.after();
* BeforeCommand - выполняет команду jQuery.before();
* AppendCommand - выполняет команду jQuery.append();
* PrependCommand - выполняет команду jQuery.prepend();
* ChangedCommand - отмечает элемент классом "ajax-changed";
* HtmlCommand - выполняет команду jQuery.html();
* DataCommand - выполняет команду jQuery.data();
* InsertCommand - выполняет команду jQuery.insert();
* RemoveCommand - выполняет команду jQuery.remove();
* ReplaceCommand - выполняет команду jQuery.replace();

Полный список команд можно посмотреть тут https://api.drupal.org/api/drupal/core%21core.api.php/group/ajax/8.2.x.

### Часть 3: Создаем свои команды.

Встроенные команды дают широкие возможности, но иногда бывает необходимо выполнить определенный набор действий отличающихся от предустановленных. Попытка решить задачу реализовав ее встроенными командами чаще всего приводит к нечитаемому и не расширяемому коду или вообще оказывается невозможной... Для подобных случаев предусмотрена возможность создания своих комманд.

Для примера создадим команду прокрутки до определенного элемента на странице со сдвигом на указанное кол-во px.

Для начала опишем нашу команду. Создаем файл src/Ajax/ScrollToCommand.php и опишем класс ScrollToCommand.

```
<?php
// Определяем область видимости.
namespace Drupal\craft\Ajax;

// Импортируем зависимости.
use Drupal\Core\Ajax\CommandInterface;

class ScrollToCommand implements CommandInterface {

  // Описываем необходимые переменные.
  protected $selector;
  protected $offset;
  protected $speed;

  // Присваивем переменным значение при создании объекта класса.
  public function __construct($selector, $offset = 0, $speed = 500) {
    $this->selector = $selector;
    $this->offset = $offset;
    $this->speed = $speed;
  }

  /**
   * Выполняем Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {
    return array(
      'command' => 'scrollTo',
      'selector' => $this->selector,
      'offset' => $this->offset,
      'speed' => $this->speed,
    );
  }

}

```

Теперь создадим файл с реализацией комманды и разместим его в папке js в корне нашего модуля. Добавим новый метод в Drupal.AjaxCommands.

```
(function ($, Drupal) {
  /**
   * Добавим новыую команду scrollTo.
   */
  Drupal.AjaxCommands.prototype.scrollTo = function (ajax, response, status) {
    // Проверяем наличие селектора в переданных данных.
    if (!response.selector) {
      return;
    }

    // Находим необходимый элемент и проверяем существует элемент на странице или нет.
    var $wrapper = $(response.selector);
    if(!$wrapper.length){
      return;
    }
    // Высчитываем положение элемента и необходимый сдвиг.
    var top = $wrapper.offset().top;
    var offset = response.offset ? response.offset : 0;

    // Получаем скорость скорллинга.
    var speed = responce.spped ? responce.spped : 500;
    // Прокручиваем страницу до высчитанной точки.
    $('html,body').stop().animate({
      scrollTop: top + offset
    }, speed);
  }
})(jQuery, Drupal);
```

В параметре response мы получаем все переданные из Drupal\craft\Ajax\ScrollToCommand::render() значения и выполняем необходимый набор действий.

Осталось дело за малым: в нашем контролере импортируем класс с описанной командой и вызываем его.

```
// Определяем область видимости нашего класса.
namespace Drupal\cusom_ajax\Controller;

// Указываем зависимости.
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\craft\Ajax\ScrollToCommand;

/**
 * Определяем контролер CustomAjaxController.
 */
class CustomAjaxController extends ControllerBase {

  public function ajax_show_message() {
    // Создаем экземпляр класса AjaxResponse().
    $response = new AjaxResponse();

    // Добавляем нашу команду scrollTo со сдвигом -90px.
    $response->addCommand(new ScrollToCommand('#custom-block-selector', -90));
    //Возвращаем набор команд для выполнения.
    return $response;
  }

}

```

На этом все. Теперь вы можете создавать свои команды. Но всегда помните, если ваша задача решается парой предустановленных комманд, то писать свою команду нет смысла.
