#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
    tx_staffdirectory_mobilephone varchar(20) DEFAULT '' NOT NULL,
    tx_staffdirectory_gender int(11) DEFAULT '0' NOT NULL,
    tx_staffdirectory_email2 varchar(255) DEFAULT '' NOT NULL,
    tx_staffdirectory_gdpr_date int(11) DEFAULT '0' NOT NULL,
    tx_staffdirectory_gdpr_proof text,
    path_segment varchar(2048),

    FULLTEXT KEY ft_name (first_name, middle_name, last_name),
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
    KEY CType (CType(250)),
);

#
# Table structure for table 'tx_staffdirectory_domain_model_organization'
#
CREATE TABLE tx_staffdirectory_domain_model_organization (
    long_name varchar(255) DEFAULT '' NOT NULL,
    short_name varchar(50) DEFAULT '' NOT NULL,
    description text,
    members int(10) unsigned DEFAULT '0' NOT NULL,
    suborganizations varchar(255) DEFAULT '' NOT NULL,
    path_segment varchar(2048),
    sorting int(10) unsigned DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_staffdirectory_domain_model_member'
#
CREATE TABLE tx_staffdirectory_domain_model_member (
    organization int(10) unsigned DEFAULT '0' NOT NULL,
    feuser_id int(10) unsigned DEFAULT '0' NOT NULL,
    position_function varchar(255) DEFAULT '' NOT NULL,
    sorting int(10) unsigned DEFAULT '0' NOT NULL,

    KEY organization (organization),
    KEY feuser_id (feuser_id)
);
