# Staffdirectory

## Suggested configuration

- Create a storage folder for your groups and persons
- Edit its Page TSconfig to have:

   ```
   mod.web_list.allowedNewTables (
       fe_users,
       tx_staffdirectory_domain_model_organization
   )
   
   mod.web_list.hideTables := addToList(tx_staffdirectory_domain_model_member)
   
   # This makes adding new fe_users much easier, adapt to your needs!
   TCAdefaults.fe_users {
       usergroup = 1
       username = nologin-
       password = __invalid__
       tx_extbase_type = tx_staffdirectory
   }
   ```
