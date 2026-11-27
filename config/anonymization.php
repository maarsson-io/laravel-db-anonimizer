<?php

return [
    /*
     * Plain-text password hashed by the password_hash rule in the copied database.
     * Use a development-only password, never a real account password.
     */
    'default_password' => 'secret',

    /*
     * Suffix appended to the source database name. The derived database is
     * recreated on every run. Start with an underscore, followed by letters,
     * digits or underscores; an empty suffix is not allowed.
     */
    'database_suffix' => '_anonymized',

    /*
     * Tables where identical original values should receive identical replacements
     * within the same run. Matching uses the complete set of columns configured
     * in anonymize_tables, not each column separately. IDs and unconfigured
     * columns do not affect matching. These tables must also be configured
     * in anonymize_tables.
     */
    'consistent_tables' => [],

    /*
     * Tables to empty completely in the copied database before anonymizing fields.
     * Use this for records that do not need to be retained for development.
     * These tables must not also appear in anonymize_tables. Tables referenced
     * by foreign keys cannot be truncated.
     */
    'truncate_tables' => [
        'failed_jobs',
        'password_resets',
        'personal_access_tokens',
    ],

    /*
     * Field replacement rules, structured as:
     * 'table_name' => ['column_name' => 'replacement_rule'].
     *
     * Only configured columns are replaced; other values and record IDs remain
     * unchanged. NULL and empty strings are preserved, except for the 'null'
     * rule, which always sets the column to NULL. All changes affect only
     * the copied database.
     */
    'anonymize_tables' => [
        'users' => [
            'name' => 'fake_name',
            'email' => 'fake_email',
            'password' => 'password_hash',
            'remember_token' => 'null',
        ],
    ],
];
