# Mibew First Message plugin

It provides a way to send custom first message as if it was sent by the user.


## Installation

1. Get the archive with the plugin sources. You can download it from the
[official site](https://mibew.org/plugins#mibew-first-message) or build the
plugin from sources.

2. Untar/unzip the plugin's archive.

3. Put files of the plugins to the `<Mibew root>/plugins`  folder.

4. (optional) Add plugins configs to "plugins" structure in
"`<Mibew root>`/configs/config.yml". If the "plugins" stucture looks like
`plugins: []` it will become:
    ```yaml
    plugins:
        "Mibew:FirstMessage": # Plugin's configurations are described below
            template: "{message}"
    ```

5. Navigate to "`<Mibew Base URL>`/operator/plugin" page and enable the plugin.

6. Alter button code and add GET parameter `first_message` to chat start URLs
(see below).


### Rendering button's code

You can provide any custom message with simple GET parameter `first_message`. To
do so you have to alter Mibew button's code. Let's assume that the original code
is (indentation are addeded for readability):

```html
<!-- mibew button -->
    <a id="mibew-agent-button" href="/chat?locale=en" target="_blank" onclick="Mibew.Objects.ChatPopups['5584079b15e45950'].open();return false;">
        <img src="/b?i=mibew&amp;lang=en" border="0" alt="" />
    </a>
    <script type="text/javascript" src="/js/compiled/chat_popup.js"></script>
    <script type="text/javascript">
        Mibew.ChatPopup.init({
            "id":"5584079b15e45950",
            "url":"\/chat?locale=en",
            "preferIFrame":true,
            "modSecurity":false,
            "width":640,
            "height":480,
            "resizable":true,
            "styleLoader":"\/chat\/style\/popup"
        });
    </script>
<!-- / mibew button -->
```

and the target site uses simple PHP files as templates system. Then altered button code is:

```php
<?php
  // The logic of message generation can depends on the target site details. A
  // simple constan is used below.
  $viewed_page = urlencode('Adventure Time comics');
?>

<!-- mibew button -->
    <a id="mibew-agent-button" href="/chat?locale=en&first_message=<?php echo($viewed_page); ?>" target="_blank" onclick="Mibew.Objects.ChatPopups['5584079b15e45950'].open();return false;">
        <img src="/b?i=mibew&amp;lang=en" border="0" alt="" />
    </a>
    <script type="text/javascript" src="/js/compiled/chat_popup.js"></script>
    <script type="text/javascript">
        Mibew.ChatPopup.init({
            "id":"5584079b15e45950",
            "url":"\/chat?locale=en&first_message=<?php echo($viewed_page); ?>",
            "preferIFrame":true,
            "modSecurity":false,
            "width":640,
            "height":480,
            "resizable":true,
            "styleLoader":"\/chat\/style\/popup"
        });
    </script>
<!-- / mibew button -->
```

If you set `template` config variable to `I'm interesting in {message}` you can
get in the chat window something like the following:

```
15:10:37 Thank you for contacting us. An operator will be with you shortly.
15:10:38 Guest: I'm interesting in Adventure Time comics.
```


## Plugin's configurations

The plugin can be configured with values in "`<Mibew root>`/configs/config.yml"
file.

### config.ignore_emoticons

Type: `String`

Default: `{message}`

Can be used to customize user's message. To show user's message `{message}`
placeholder can be used inside of the template. This value is optional and can
be skipped.


## Build from sources

There are several actions one should do before use the latest version of the
plugin from the repository:

1. Obtain a copy of the repository using `git clone`, download button, or
another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Install npm dependencies using `npm install`.
5. Run Gulp to build the sources using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use Plugin will be
available in `release` directory.


## License

[Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
