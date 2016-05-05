# Quick start

This SDK contains three primary components:
- [**Elements**](#elements) - Instant Article Markup renderer
- [**Transformer**](#transformer) - Engine which transforms HTML into **Elements** objects
- [**Client**](#client) - Client to publish Instant Articles

[**Transformer Rules**](#custom-transformer-rules) - Within the Transformer are rules which define a mapping between elements in the *source markup* and valid *Instant Article components*. These rules are customizable to allow flexibility in the interpretation of the source markup and is a crucial part of the transformation process so it has a dedicated section.

---

## Elements
`Elements` is the object tree class that represents the structure of an Instant Article. This object tree structure ensures that no invalid Instant Article HTML markup is generated. Here is a simple and complete object tree structure, starting with the `InstantArticle` class that holds the full Instant Article.

```php
$fragment = $document->createDocumentFragment();
$fragment->appendXML(
    '<h1>Some custom code</h1>'.
    '<script>alert("test");</script>'
);
$article =
    InstantArticle::create()
        ->withCanonicalUrl('http://foo.com/article.html')
        ->withHeader(
            Header::create()
                ->withTitle('Big Top Title')
                ->withSubTitle('Smaller SubTitle')
                ->withPublishTime(
                    Time::create(Time::PUBLISHED)
                        ->withDatetime(
                            \DateTime::createFromFormat(
                                'j-M-Y G:i:s',
                                '14-Aug-1984 19:30:00'
                            )
                        )
                )
                ->withModifyTime(
                    Time::create(Time::MODIFIED)
                        ->withDatetime(
                            \DateTime::createFromFormat(
                                'j-M-Y G:i:s',
                                '10-Feb-2016 10:00:00'
                            )
                        )
                )
                ->addAuthor(
                    Author::create()
                        ->withName('Author Name')
                        ->withDescription('Author more detailed description')
                )
                ->addAuthor(
                    Author::create()
                        ->withName('Author in FB')
                        ->withDescription('Author user in facebook')
                        ->withURL('http://facebook.com/author')
                )
                ->withKicker('Some kicker of this article')
                ->withCover(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        ->withCaption(
                            Caption::create()
                                ->appendText('Some caption to the image')
                        )
                )
        )
        // Paragraph1
        ->addChild(
            Paragraph::create()
                ->appendText('Some text to be within a paragraph for testing.')
        )
        // Paragraph2
        ->addChild(
            Paragraph::create()
                ->appendText('Other text to be within a second paragraph for testing.')
        )
        // Slideshow
        ->addChild(
            SlideShow::create()
                ->addImage(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                )
                ->addImage(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home2.jpg')
                )
                ->addImage(
                    Image::create()
                        ->withURL('https://jpeg.org/images/jpegls-home3.jpg')
                )
        )
        // Paragraph3
        ->addChild(
            Paragraph::create()
                ->appendText('Some text to be within a paragraph for testing.')
        )
        // Ad
        ->addChild(
            Ad::create()
                ->withSource('http://foo.com')
        )
        // Paragraph4
        ->addChild(
            Paragraph::create()
                ->appendText('Other text to be within a second paragraph for testing.')
        )
        // Analytics
        ->addChild(
            Analytics::create()
                ->withHTML($fragment)
        )
        // Footer
        ->withFooter(
            Footer::create()
                ->withCredits('Some plaintext credits.')
        );
```

### Rendering the `InstantArticle` Markup

From above, `$article` now contains a complete `InstantArticle` object — a structured representation of an Instant Article — which can be rendered into valid Instant Article HTML Markup by simply calling its `render()` function:

```php
$article->render('<!doctype html>');
```

#### Rendered output of the `InstantArticle` object from above
```xml
<!doctype html>
<html>
<head>
    <link rel="canonical" href="http://foo.com/article.html"/>
    <meta charset="utf-8"/>
    <meta property="op:markup_version" content="v1.0"/>
    <meta property="fb:use_automatic_ad_placement" content="true"/>
</head>
<body>
    <article>
        <header>
            <figure>
                <img src="https://jpeg.org/images/jpegls-home.jpg"/>
                <figcaption>Some caption to the image</figcaption>
            </figure>
            <h1>Big Top Title</h1>
            <h2>Smaller SubTitle</h2>
            <time class="op-published" datetime="1984-08-14T19:30:00+00:00">August 14th, 7:30pm</time>
            <time class="op-modified" datetime="2016-02-10T10:00:00+00:00">February 10th, 10:00am</time>
            <address>
                <a>Author Name</a>
                Author more detailed description
            </address>
            <address>
                <a href="http://facebook.com/author" rel="facebook">Author in FB</a>
                'Author user in facebook'.
            </address>
            <h3 class="op-kicker">Some kicker of this article</h3>
        </header>
        <p>Some text to be within a paragraph for testing.</p>
        <p>Other text to be within a second paragraph for testing.</p>
        <figure class="op-slideshow">
            <figure>
                <img src="https://jpeg.org/images/jpegls-home.jpg"/>
            </figure>
            <figure>
                <img src="https://jpeg.org/images/jpegls-home2.jpg"/>
            </figure>
            <figure>
                <img src="https://jpeg.org/images/jpegls-home3.jpg"/>
            </figure>
        </figure>
        <p>Some text to be within a paragraph for testing.</p>
        <figure class="op-ad">
            <iframe src="http://foo.com"></iframe>
        </figure>
        <p>Other text to be within a second paragraph for testing.</p>
        <figure class="op-tracker">
            <iframe>
                <h1>Some custom code</h1>
                <script>alert("test");</script>
            </iframe>
        </figure>
        <footer>
            <aside>Some plaintext credits.</aside>
        </footer>
    </article>
</body>
</html>
```

## Transformer
The `Transformer` interprets *any* markup in order to fill in the [`InstantArticle`](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/src/Facebook/Instantarticles/Elements/InstantArticle.php) object structure. The transformation process follows a set of pre-defined selector rules which maps the markup of the input to known `InstantArticles` `Elements`. This user-defined configuration makes the Transformer versatile and powerful.

### Transformer Configuration

The power of the **Transformer** lies in the configuration rules it uses to map elements from the input markup to Instant Article markup. Configuration rules are applied ***bottom-up*** so all new or custom rules should be added at the end of the file.

- Each rule in the configuration file should live in the `rules` array
- Each entry should have at least the `class` attribute set
- All classes referred by this configuration file must implement the [`Rule`](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/src/Facebook/InstantArticles/Transformer/Rules/Rule.php) class

The transformer pseudo-algorithm is:

```php
$document = loadHTML($input_file);
foreach($document->childNodes as $node) {
    foreach($rules as $rule) {
        if ($rule->matches($context, $node)) {
            // Apply rule...
        }
    }
}
```

This transformer will run through all elements, and for each element checking all rules. The rule to be applied will need to match 2 conditions:

- Matches context
- Matches selector

#### Matching context
Context is the container element that is now in the pipe being processed. This is returned by the method:

```php
public function getContextClass() {
    return InstantArticle::getClassName();
}
```

If the `Rule` will be handling more than one context, it is possible by returning an array of classes:

```php
public function getContextClass() {
    return array(InstantArticle::getClassName(), Header::getClassName());
}
```

#### Matching selector
The **selector** field will be used only by rules that extend [`ConfigurationSelectorRule`](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/src/Facebook/InstantArticles/Transformer/Rules/ConfigurationSelectorRule.php).

The selector field will be used as a *CSS selector*; or as an *Xpath selector* if beginning with `/`.

**Example: using a *CSS Selector* to match a rule**
```javascript
{
    "class": "HeaderRule",
    "selector" : "div.header"
}
```

**Example: using an *Xpath Selector* to match a rule**
```javascript
{
    "class": "HeaderRule",
    "selector" : "//div[class=header]"
}
```

### Example

#### Input HTML
The following markup is a sample of what could be used as input to the Transformer:

```html
<html>
    <head>
        <script type="text/javascript" href="http://domain.com/javascript.js" />
    </head>
    <body>
        <div class="header">
            <div class="title">
                <h1>The article title</h1>
                <h2>Sub Title</h2>
                <span class="author">Author name</author>
            </div>
            <div class="hero-image">
                <img src="http://domain.com/image.png" />
                <div class="image-caption">
                  Some amazing moment captured by Photographer
                </div>
            </div>
        </div>
        <p>Lorem <b>ipsum</b> dolor sit amet, consectetur adipiscing elit. Sed eu arcu porta, ultrices massa ut, porttitor diam. Integer id auctor augue.</p>
        <p>Vivamus mattis, sem id consequat dapibus, odio urna fermentum risus, in blandit dolor justo vel ex. Curabitur a neque bibendum, hendrerit sem in, congue lectus.</p>
        <div class="image">
            <img src="http://domain.com/image.png" />
            <div class="image-caption">
              Some amazing moment captured by Photographer
            </div>
        </div>
        <p>Curabitur vulputate odio eu justo <i>venenatis</i>, a pretium orci placerat. Nam sed neque quis eros vestibulum mattis. Donec vitae mi egestas, laoreet massa et, fringilla libero.</p>
    </body>
</html>
```

#### Full rule configuration file for the HTML above

This rule configuration will:

- run bottom-up
- check if matches "class" (context)
- check if matches "selector" (css or xpath)
- Run the rule (calling the callback method `transform()`)

```javascript
{
    "rules" :
        [
            {
                "class": "TextNodeRule"
            },
            {
                "class": "PassThroughRule",
                "selector" : "html"
            },
            {
                "class": "PassThroughRule",
                "selector" : "head"
            },
            {
                "class": "PassThroughRule",
                "selector" : "script"
            },
            {
                "class": "PassThroughRule",
                "selector" : "body"
            },
            {
                "class": "ItalicRule",
                "selector" : "i"
            },
            {
                "class": "BoldRule",
                "selector" : "b"
            },
            {
                "class": "ParagraphRule",
                "selector" : "p"
            },
            {
                "class": "HeaderTitleRule",
                "selector" : "div.title h1"
            },
            {
                "class": "HeaderSubTitleRule",
                "selector" : "div.title h2"
            },
            {
                "class": "HeaderRule",
                "selector" : "div.header"
            },
            {
                "class": "AuthorRule",
                "selector" : "span.author",
                "properties" : {
                    "author.name" : {
                        "type" : "string",
                        "selector" : "span"
                    }
                }
            },
            {
                "class": "CaptionRule",
                "selector" : "div.image-caption"
            },
            {
                "class": "ImageRule",
                "selector" : "div.image",
                "properties" : {
                    "image.url" : {
                        "type" : "string",
                        "selector" : "img",
                        "attribute": "src"
                    },
                    "image.caption" : {
                        "type" : "element",
                        "selector" : "div.image-caption"
                    }
                }
            },
            {
                "class": "HeaderImageRule",
                "selector" : "div.hero-image",
                "properties" : {
                    "image.url" : {
                        "type" : "string",
                        "selector" : "img",
                        "attribute": "src"
                    },
                    "image.caption" : {
                        "type" : "element",
                        "selector" : "div.image-caption"
                    }
                }
            }
        ]
}
```

### Creating Custom Rules
Each custom rule implemented should comply with full contract of the `Rule` abstract class.

```php
class MyCustomRule extends Rule
{
    public function matchesContext($context)
    {}

    public function matchesNode($node)
    {}

    public function apply($transformer, $container, $node)
    {}
}
```

The best option is to use the `ConfigurationSelectorRule` as base class for all custom Rules. This way the selector and more configurations are inherited by default.

### Invoking Transformer

To transform your markup into InstantArticle markup, follow these steps:

- Create an `InstantArticle` instance
- Create a `Transformer` and load it with rules (programmatically or from a file)
- Load/retrieve the HTML content file in the original markup
- Run the Transformer
- Check for errors/warnings

#### Example
```php
// Loads the rules content file
$rules_file_content = file_get_contents("simple-rules.json", true);

// Instantiate Instant article
$instant_article = InstantArticle::create();

// Creates the transformer and loads the rules
$transformer = new Transformer();
$transformer->loadRules($rules_file_content);

// Example loads the html from a file
$html_file = file_get_contents("simple.html", true);

// Ignores errors on HTML parsing
libxml_use_internal_errors(true);
$document = new \DOMDocument();
$document->loadHTML($html_file);
libxml_use_internal_errors(false);

// Invokes transformer
$transformer->transform($instant_article, $document);

// Get errors from transformer
$warnings = $transformer->getWarnings();

// Renders the InstantArticle markup format
$result = $instant_article->render();
```

## Client

The API Client is a lightweight layer on top of the [Facebook SDK for PHP](https://github.com/facebook/facebook-php-sdk-v4) making it easy to push articles to your Facebook Page. Example:

```php
$article = InstantArticle::create();
$transformer->transform($article, $someDocument);

// Instantiate an API client
$client = Client::create(
    'APP_ID'
    'APP_SECRET',
    'ACCESS_TOKEN',
    'PAGE_ID',
    false // development envirorment?
);

// Import the article
try {
    $client->importArticle($article, $take_live);
} catch (Exception $e) {
    echo 'Could not import the article: '.$e->getMessage();
}
```

### `Helper` class

Since publishing Instant Articles is done to an existing Facebook Page, the `Client` also contains a `Helper` class to simplify fetching the access token for Facebook Pages that you're an admin of. Example:

```php
$userAccessToken = 'USER_ACCESS_TOKEN';

// Instantiate a client helper
$helper = Helper::create(
    'APP_ID',
    'APP_SECRET'
);

// Grab pages you are admin of and tokens
$pagesAndTokens = $helper->getPagesAndTokens($userAccessToken)->all();
foreach ($pagesAndTokens as $pageAndToken) {
    echo 'Page ID: ' . $pageAndToken->getField('id');
    echo 'Page name: ' . $pageAndToken->getField('name');
    echo 'Page access token: ' . $pageAndToken->getField('access_token');
}
```

---

## Transformer Rules

### Overview

A valid Instant Article is comprised of a subset of standard HTML tags, detailed in the [Format Reference](https://developers.facebook.com/docs/instant-articles/reference). Adhering to these restrictions ensures that content renders reliably and performant on mobile devices within Facebook but naturally constrains what is allowed within the markup. What's more, the hierarchy of the permitted tags also matters.

> For example, to render text **bold** in an Instant Article, the `<strong>` tag *must* be used. But if your content makes use of `<b>` as a means to stylize text bold, you would find that your source markup is *valid HTML*, but ultimately is not *valid Instant Articles markup*.

The Transformer of this SDK helps mitigate these constraints by converting *any markup* into *Instant Articles markup*, and it's the ***Transformer Rules*** which instructs it on how to do so. Collectively, these rules form a mapping between elements in the source markup and what they should be transformed into within the generated Instant Article. Analogous to a car, if the Transformer were the engine powering the conversion of the markup, the Transformer Rules would be the driver.

Many [example rules](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/tests/Facebook/InstantArticles/Transformer/instant-article-example-rules.json) have been defined which aim to cover most common scenarios. Additional rules can be added to amend or override existing ones.

### <a name="custom-transformer-rules"></a>Configuring Transformer Rules

At a high level, configuring a Transformer Rule involves three steps:

1. Identifying a source element in your markup
2. Associating it with an existing [Transformer Rule Class](#transformer-classes) in a permitted context
3. Defining any value for attributes on the transformed element that the Rule Class expects (not always needed)

Take the following example which would cause text within `<span class="bold">` to be stylized **bold** in the generated Instant Article.

```javascript
// Transformer Rule associating <span class="bold"> to the `BoldRule` class
{
  "class": "BoldRule",
  "selector": "span.bold"
  //"properties": {} // Not needed since the `BoldRule` class has no properties
}
```

*If you're curious, the resulting markup within the Instant Article for the `BoldRule` class is the `<strong>` tag; the fact that this detail is abstracted by the Transformer is intentional.*

> Caveat: the example above makes no mention of *context*. It turns out, the rule above would only work as expected if it were being processed within a particular parent context of the generated document. Read more about [rule context](#rule-context).

#### Transformer Rule: Selector

The selector is used to identify elements in the source markup. Both **CSS selectors** and **Xpath** are supported formats.

#### Transformer Rule: Properties

Some Transformer Rules have properties who's value are obtained from content in the source markup. The content itself isn't specified in the rule; rather, the rule defines where in the source element the value is located, again by use of a selector.

In the configuration of your rule, you instruct the transformer where the value can be obtained from within the source markup with the following key-value pairs allowed for a property:

Key name | Required? | Notes
--- | --- | ---
`type` | *Required* | Defines the behavior of the `selector` (below). Possible values: `"string"`, `"int"`, `"exists"`, `"sibling"`, `"children"`, `"xpath"`.
`selector` | *Required* | The interpretation of the value of this item is affected by the `type` (above).
`attribute` | *Required only if `type` is one of: `"string"`, `"int"`, `"exists"`, `"xpath"`* | The name of the attribute on the element, identified by the value in `selector`, whose value you want to use as the content for the property of the Transformer Rule.<br><br>When `type` is `"exists"`, the value of the property will be `true` simply by the presence of the attribute, regardless of its value.

##### Examples

For example, take the standard case of configuring the transformer to recognize anchor tags, such as `<a href="http://example.com">` for generating links with an Instant Article:

```javascript
{
 "class": "AnchorRule",  // Rule class
 "selector": "a",        // Matching source element (CSS selector)
 "properties" : {        // All supported properties

   "anchor.href" : {     // Name of property
     "type" : "string",
     "selector" : "a",   // Additional selector specific to this property
     "attribute": "href" // Attribute whose value to use
   }

 }
}
```

As a contrived example, here's a rule which would transform source markup formatted as such: `<span class="custom-href" data-link="http://example.com">` into the same links within the generated Instant Article:

```javascript
{
 "class": "AnchorRule",
 "selector": "span.custom-href",
 "properties" : {

   "anchor.href" : {
     "type" : "string",
     "selector" : "span",
     "attribute": "data-link"
   }

 }
}
```

#### <a name="transformer-classes"></a>Transformer Rule: Class

A *Rule Class* is simply a pre-determined link to a valid Instant Article component. It defines supported `properties` and permitted *context(s)* for the Rule.

> <a name="rule-context"></a> **Rule Context**

> The hierarchal nature of an HTML document implies that an element always exists within the *context of a parent element*. A similar hierarchy is built during a document's transformation into an Instant Article giving each individual element a *context*. This context plays an important role for the Transfer Rules since, along with the selector, it is a condition that must be matched before a rule is executed.

> As the Transformer traverses through the entire HTML document, it attempts to execute all of rules for every tag element it encounters. But two criteria need to be met each time:

> 1. the *selector* of the rule must match the current element
> 2. the *context* in which the rule would run must match one of the allowed context(s) of the rule class

> In other words, as the Transformer progresses, it uses the rules to build a hierarchy of transformed elements, giving *context* to each subsequent rule. Rules are only permitted to execute within an allowed *context* defined for the [Rule Class](#transformer-classes) is uses.

##### Available Rule Classes

Listed below are all the available *Rule Classes*, along with their supported `properties` and permitted *context(s)*, whereby source markup can be mapped to a valid Instant Article component via its selectors. They are arranged into logical groups by function.

**Formatting**

Transformer Rule Class | Properties | Permitted Context | Notes
--- | --- | --- | ---
`AnchorRule` | `anchor.href`<br>`anchor.rel` | *TextContainer* | 
`BoldRule` |  | *TextContainer* | 
`ItalicRule` |  | *TextContainer* | 
`LineBreakRule` |  | *TextContainer* | 

**Layout**

Transformer Rule Class | Properties | Permitted Context | Notes
--- | --- | --- | ---
`BlockquoteRule` |  | *InstantArticle* | 
`H1Rule` |  | *Caption*, *Header*, *InstantArticle* | 
`H2Rule` |  | *Caption*, *Header*, *InstantArticle* | 
`HeaderRule` |  | *InstantArticle* | Can apply to all headers (`HeaderKickerRule`, `HeaderSubTitleRule`, `HeaderTitleRule`)
`ListElementRule` |  | *InstantArticle* | 
`ListItemRule` |  | *ListElement* | 
`ParagraphRule` |  | *InstantArticle* | 
`PullquoteCiteRule` |  | *Pullquote* | 
`PullquoteRule` |  | *InstantArticle* | 

**Graphic**

Transformer Rule Class | Properties | Permitted Context | Notes
--- | --- | --- | ---
`AdRule` | `ad.url`<br>`ad.height`<br>`ad.width`<br>`ad.embed` | *InstantArticle* |
`AnalyticsRule` | `analytics.url`<br>`analytics.embed` | *InstantArticle* |
`GeoTagRule` | `map.geotag` | *Image*, *Video*, *Map* | 
`HeaderAdRule` | `ad.url`<br>`ad.height`<br>`ad.width`<br>`ad.embed` | *Header* |
`HeaderImageRule` | `image.url` | *Header* | 
`ImageRule` | `image.url`<br>`image.like`<br>`image.comments` | *InstantArticle* |
`InteractiveRule` | `interactive.iframe`<br>`interactive.url`<br>`interactive.height`<br>`no-margin`<br>`column-width` | *InstantArticle* | 
`MapRule` |  | *InstantArticle* | 

**Media**

Transformer Rule Class | Properties | Permitted Context | Notes
--- | --- | --- | ---
`AudioRule` | `audio.url`<br>`audio.title`<br>`audio.autoplay`<br>`audio.muted` | *Audible* |
`RelatedArticlesRule` | `related.title` | *InstantArticle* | 
`RelatedItemRule` | `related.sponsored`<br>`related.url` | *RelatedArticles* |
`SlideshowImageRule` | `image.url`<br>`caption.title`<br>`caption.credit` | *Slideshow* |
`SlideshowRule` |  | *InstantArticle* | 
`SocialEmbedRule` | `socialembed.iframe`<br>`socialembed.url` | *InstantArticle* |
`VideoRule` | `video.url`<br>`video.type`<br>`video.playback`<br>`video.controls`<br>`video.like`<br>`video.comments` | *InstantArticle* |

**Article Structure**

Transformer Rule Class | Properties | Permitted Context | Notes
--- | --- | --- | ---
`AuthorRule` | `author.url`<br>`author.name`<br>`author.description`<br>`author.role_contribution` | *Header* | 
`CaptionCreditRule` |  | *Caption* | 
`CaptionRule` | `caption.default` | *Map*, *Image*, *Interactive*, *Slideshow*, *SocialEmbed*, *Video* | 
`FooterRelatedArticlesRule` | `related.title` | *Footer* | 
`FooterRule` |  | *InstantArticle* | 
`HeaderKickerRule` |  | *Header* | 
`HeaderSubTitleRule` |  | *Header* | 
`HeaderTitleRule` |  | *Header* | 
`ParagraphFooterRule` |  | *Footer* | 
`TimeRule` | `article.time`<br>`article.time_type` | *Header* |

**Special**

Transformer Rule Class | Properties | Permitted Context | Notes
--- | --- | --- | ---
`IgnoreRule` |  | *(any)* | This rule class will effectively strip out an element tag which matches the associated ***selector*** of the rule.
`PassThroughRule` |  | *(any)* | This rule class instructs the Transformer to not process any transformation on element tags which match the associated  ***selector*** of the rule.
`InstantArticleRule` | `article.canonical`<br>`article.charset`<br>`article.markup.version`<br>`article.auto.ad` | *InstantArticle* | This is the entry point, or root node, of the hierarchy.
`TextNodeRule` |  | *TextContainer* | 
