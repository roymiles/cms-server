# Zeal web development project

## Summary
Zeal is a web development project to allow clients to create fully database driven websites without having any back end code on their web host. This is done through a javascript framework that authenticates a websites request to access, modify and delete different aspects of the their website to enable dynamic webpages.

At the moment, this project is in its very early stages and is a mere proof of concept. I may take this project in a different direction as I see fit.

The project is written with the [Symfony2](https://symfony.com/) framework and has made extensive use of their FOS bundles to speed up development.

**Main point and goal of this project is that Everything (modules, actions etc) written and used on this site has to be able to be used externally on other websites.** All references will be done through the SiteId where SiteId = -1 will be for zela.io directly with local session storage and SiteId != -1 will be for external sites that use AccessTokens. 

## TODO

### General
- [ ] Generic table interface and template
- [ ] Sort table by columns
- [ ] Generic pagination widget
- [ ] Inline editable tables
- [ ] Authentication system for table edits (generic interface?)
- [ ] Remove github markdown css

### Documentation
- [x] Get JBBCode markdown working
- [ ] Breadcumb links
- [ ] Child documentation links in a widget
- [ ] **BBCode for inline API responses**

### Javascript framework
- [ ] Integrate a secure and tested OAUTH2 library
- [ ] Create a session management service
- [ ] Get the login
- [ ] Registration system working

### Small tasks
- [ ] Seperate tables for external and internal users
- [ ] Get basic API routes working with FOSRestBundle
- [ ] Get API documentation working with nelmio bundle

### Testing
- [ ] Try out the built in symfony2 logging system



*By Roy Miles*






