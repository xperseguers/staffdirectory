# Staffdirectory

## Suggested configuration

- Create a storage folder for your groups and persons
  - Edit its Page TSconfig to have:

     ```
     mod.web_list.allowedNewTables (
         fe_users,
         tx_staffdirectory_domain_model_organization
     )

     # This makes adding new fe_users much easier, adapt to your needs!
     TCAdefaults.fe_users {
         usergroup = 1
         username = nologin-
         password = __invalid__
         country = CH
         tx_extbase_type = tx_staffdirectory
     }
     ```


## Routing configuration

Nice URL can be configured by editing your site configuration (stored in
file `config/sites/<site>/config.yaml`):

```
routeEnhancers:
  Staffdirectory:
    type: Extbase
    limitToPages:
      - <detail-page-of-a-person>
      - <detail-page-of-an-organization>
    extension: Staffdirectory
    plugin: Plugin
    routes:
      -
        routePath: '/p/{person-name}'
        _controller: 'Plugin::person'
        _arguments:
          person-name: person
      -
        routePath: '/o/{organization-name}'
        _controller: 'Plugin::organization'
        _arguments:
          organization-name: organization
    aspects:
      person-name:
        type: PersistedAliasMapper
        tableName: fe_users
        routeFieldName: path_segment
      organization-name:
        type: PersistedAliasMapper
        tableName: tx_staffdirectory_domain_model_organization
        routeFieldName: path_segment
```

Note: you may omit the `limitToPages` configuration but are advised to keep it.


## Sitemap configuration

You may generate a sitemap for your staff directory by adding the following
to your site's TypoScript (requires EXT:seo):

```
plugin.tx_seo.config.xmlSitemap.sitemaps {
    persons {
        provider = Causal\Staffdirectory\Seo\PersonsXmlSitemapDataProvider
        config {
            pid = PERSONS_STORAGE_UID
            recursive = 0
            url {
                pageId = SINGLE_PERSON_PAGE_UID
                fieldToParameterMap {
                    uid = tx_staffdirectory_plugin[person]
                }

                additionalGetParameters {
                    tx_staffdirectory_plugin.controller = Plugin
                    tx_staffdirectory_plugin.action = person
                }

                useCacheHash = 1
            }
        }
    }
}
```

You should naturally adapt `PERSONS_STORAGE_UID` and `SINGLE_PERSON_PAGE_UID`
to your actual page UIDs.

Further reading:
https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Seo/XmlSitemap.html
