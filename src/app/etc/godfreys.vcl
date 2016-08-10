# Varnish configuration for Magento with FPC powered by Varnish module installed
# see http://www.magentocommerce.com/magento-connect/pagecache-powered-by-varnish.html

backend godfrey_dev {
    .host = "127.0.0.1";
    .port = "8080";
    .connect_timeout = 16000s;
    .first_byte_timeout = 16000s;
    .between_bytes_timeout = 16000s;
}                     

#director dir random {
#    .retries = 5;
#    {.backend = godfrey_dev; .weight = 10;}
#}

acl purge {
  "localhost";
  "127.0.0.1";
}

# Add user-agent type to cache for Mobile devices 
# Detect the device
sub detect_device {
  # Define the desktop device
  set req.http.X-Device = "desktop";

  if (req.http.User-Agent ~ "iP(hone|od)" || req.http.User-Agent ~ "Android" || req.http.User-Agent ~ "iPad") {
    # Define smartphones and tablets
    set req.http.X-Device = "smart";
  }

  elseif (req.http.User-Agent ~ "SymbianOS" || req.http.User-Agent ~ "^BlackBerry" || req.http.User-Agent ~ "^SonyEricsson" || req.http.User-Agent ~ "^Nokia" || req.http.User-Agent ~ "^SAMSUNG" || req.http.User-Agent ~ "^LG") {
    # Define every other mobile device
    set req.http.X-Device = "other";
  }
}

sub vcl_recv {
#    uncomment following to disable caching	
#  return (pass);

if (req.http.User-Agent ~ "www.baidu.com") {
   error 403 "Not allowed.";
}

# do not cache any page has url param no_cache
    if (req.url ~ "NO_CACHE") {
        return (pass);
    }
    
    if (req.http.host ~ "vacspare") {
        return (pass);
    }

if (req.http.User-Agent ~ "iP(hone|od)" || req.http.User-Agent ~ "Android" || req.http.User-Agent ~ "iPad") {
    return (pass);
  }

#    set req.backend = dir;
    set req.backend = godfrey_dev;

    # Set X-Forwared-For to client IP address. We need this because we use mod_rpaf on Apache backend
    if (!req.http.X-Forwarded-For ) {
        set req.http.X-Forwarded-For = client.ip;
    } 
    
    #################################################
    # Specific configuration for FPC module
    #################################################

    # purge request from Magento backend
    if (req.request == "PURGE") {
        if (!client.ip ~ purge) {
            error 405 "Not allowed.";
        }
        ban("obj.http.X-Purge-Host ~ " + req.http.X-Purge-Host + " && obj.http.X-Purge-URL ~ " + req.http.X-Purge-Regex + " && obj.http.Content-Type ~ " + 
req.http.X-Purge-Content-Type);
        error 200 "Purged.";
    }

    if (req.url ~ "pdf" || req.url ~"downloads_files" || req.url ~"exportCsv")
    {
        return (pipe);
    }

    # as soon as we have a NO_CACHE cookie pass request
    if (req.http.cookie ~ "NO_CACHE=") {
        return (pass);
    }

    ##################################################
    # End of Specific configuration for FPC module
    ##################################################

    # Our application does not manage other methods than HEAD, GET and POST
    # Warning : if you use REST webservices, add DELETE and PUT to this list
    if (req.request != "GET" &&
        req.request != "HEAD" &&
        req.request != "POST" ) {
        
        error 405 "Method not allowed.";
    }

    # Call the common conditions to avoid caching request and response
    # based on request parameters 

    # Only GET and HEAD can be cached, so pass everything else directly
    # to the backend.
    if (req.request != "GET" && req.request != "HEAD") {
        return (pass);
    }

    # Do not check in the cache for AJAX requests, checkout and cart pages, etc...
    if (req.url ~ "^/(index.php/)?(admin|sales|wishlist|review|checkout|customer|api|newsletter|service|contacts|googlebase|downloads|feeds|warranty|awafptc)/") {
        return (pass);
    }
    

    if (req.url ~ "^/downloads/*" ) {
	return (pass);
    }

    # The grace period allow to serve cached entry after expiration while 
    # a new version is being fetched from the backend
    set req.grace = 30s;

        
    # Each cache entry on Varnish is based on a key (provided by vcl_hash)
    # AND the Vary header. This header, sent by the server, define on which
    # client header the cache entry must vary. And for each different value of
    # the specified client header, a new cache entry on the same key will be created.
    # 
    # In case of compression, the mod_deflate on the Apache backend will add 
    # "Vary: Accept-Encoding", as some HTTP client does not support the compression
    # and some support only gzip, and some gzip and deflate. The last ones are the
    # majority but they do not advertise "gzip" and "deflate" in the same order. So to avoid
    # storing a different cache for "gzip,deflate" and "deflate,gzip", we turn the
    # accept-encoding into just "gzip".
    # We do not take into account "deflate" only browsers, as they have only a theorical
    # existence ;) Worst case: they will receive the uncompressed format.
    # 
    # So at the end we would have only 2 versions for the same cache entry:
    #     - gziped
    #     - uncompressed
    if (req.http.Accept-Encoding) {
        if (req.http.Accept-Encoding ~ "gzip") {
          set req.http.Accept-Encoding = "gzip";
        } else {
            remove req.http.Accept-Encoding;
        }
    }

   # Add user-agent type to cache for Mobile devices
   call detect_device;

    # by default for all the rest, we try to serve from the cache
    return (lookup);
 }


#
# vcl_fetch is executed when the response come back from the backend
#
sub vcl_fetch {
    # Only GET and HEAD can be cached, so pass everything else directly
    # to the backend.
    if (req.request != "GET" && req.request != "HEAD") {
        return (hit_for_pass);
    }
    
    # Do not check in the cache for AJAX requests, checkout and cart pages, etc...
    if (req.url ~ "^/(index.php/)?(admin|sales|wishlist|review|checkout|customer|api|newsletter|service|contacts|googlebase|downloads|awafptc)/") {
        return (hit_for_pass);
    }

    set beresp.grace = 30s;

    # Define cache time depending on type, URL or status code
    if (beresp.status == 301 || (beresp.status >=400 &&  beresp.status < 500)) {
    # Permanent redirections and client error are not cached
       return (hit_for_pass);
    }


    #################################################
    # Specific configuration for FPC module
    #################################################

    # add ban-lurker tags to object
    set beresp.http.X-Purge-URL = req.url;
    set beresp.http.X-Purge-Host = req.http.host;

    set beresp.http.X-ttl = beresp.ttl;
    
    if (beresp.status == 200 || beresp.status == 301 || beresp.status == 404) {
        if (beresp.http.Content-Type ~ "text/html" || beresp.http.Content-Type ~ "text/xml") {
            if ((beresp.http.Set-Cookie ~ "NO_CACHE=") || (beresp.ttl < 1s)) {
                set beresp.ttl = 0s;
                return (hit_for_pass);
            }

            # marker for vcl_deliver to reset Age:
            set beresp.http.magicmarker = "1";
            
            # Don't cache cookies
            unset beresp.http.set-cookie;
        } else {
            # set default TTL value for static content
            set beresp.ttl = 4h;
        }
            unset beresp.http.Set-Cookie;
    return (deliver);
    }

    #################################################
    # End of Specific configuration for FPC module
    #################################################
}

# Add user-agent type to cache for Mobile devices
sub vcl_hash {
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }

  # And then add the device to the hash (if its a mobile device)
#  if (req.http.X-Device ~ "smart" || req.http.X-Device ~ "other") {
    hash_data(req.http.X-Device); 
#  }

    return (hash);
}

#
# vcl_deliver is called when sending the response to the client.
# Some headers are added to help debug
#
#####################################################
# Completely copied from FPC module
#####################################################

sub vcl_deliver {
    # debug info
    if (resp.http.X-Cache-Debug) {
        if (obj.hits > 0) {
            set resp.http.X-Cache = "HIT";
            set resp.http.X-Cache-Hits = obj.hits;
        } else {
           set resp.http.X-Cache = "MISS";
        }
        set resp.http.X-Cache-Expires = resp.http.Expires;
    }
 else {
        # remove Varnish/proxy header
        remove resp.http.X-Varnish;
        remove resp.http.Via;
        remove resp.http.Age;
        remove resp.http.X-Purge-URL;
        remove resp.http.X-Purge-Host;
    }
    
    if (resp.http.magicmarker) {
        # Remove the magic marker
        unset resp.http.magicmarker;

        set resp.http.Cache-Control = "no-store, no-cache, must-revalidate, post-check=0, pre-check=0";
        set resp.http.Pragma = "no-cache";
        set resp.http.Expires = "Mon, 31 Mar 2008 10:00:00 GMT";
        set resp.http.Age = "0";
    }
}

sub vcl_error {
    set obj.http.Content-Type = "text/html; charset=utf-8";
    synthetic {"
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>Server maintenance</title>
  </head>
  <body>
    <h1>Sorry, server is unavailable due to the scheduled maintenance.</h1>
  </body>

</html>
"};
    return (deliver);
}
