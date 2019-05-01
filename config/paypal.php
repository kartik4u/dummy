<?php

return [
    /** set your paypal credential * */
    'client_id' => 'AcJ0HvG0fMEJblx1wl12_LPAFK6XrVLqGuRxQp9pAGh-VlWJFxxFfyGzHDDMuAtALA9PuXy_OOKp8NT_',
    'secret' => 'EAQ3QlIo2oGAHGeOB8YCCRtSfg275T-ChqT8zWhhcpEooFvPmObWWfbTk9yGUVWhl3WzkUb2acr5swp4',
    /**
     * SDK configuration 
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => 'sandbox',
        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 1000,
        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',
        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'FINE'
    ),
];
