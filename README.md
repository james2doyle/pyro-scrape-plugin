pyro-scrape-plugin
==================

This is a simple plugin that scrapes a URL and returns an array of results.

```html
<ul>
  {{ scrape:find url="http://www.example.com/page-with-a-html-table" selectors="table|td" cache_duration="10080" }}
    <li>{{ td }}</li>
  {{ /scrape:find }}
</ul>
```

This example broken down:

* scrape the url
* find the first table
* get whats inside the td tag
* save the results for 7 days (24 * 7 * 60)
* display the results in a list

There is a small info array set up in the plugin so you can see how it works. It's pretty short.