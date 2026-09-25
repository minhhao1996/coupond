<?php
return ['enabled'=>env('ANALYTICS_ENABLED',true), 'country_lookup_enabled'=>env('ANALYTICS_COUNTRY_LOOKUP_ENABLED',true), 'country_database'=>env('ANALYTICS_COUNTRY_DATABASE',storage_path('app/geoip/dbip-country-lite.mmdb')), 'retention_days'=>90, 'ip_retention_days'=>30];
