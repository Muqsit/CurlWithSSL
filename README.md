# CurlWithSSL
PocketMine plugins<sup>[[tebexio/BuycraftPM](https://github.com/tebexio/BuycraftPM/issues/48) and [many others](https://github.com/search?q=pocketmine+CURLOPT_SSL_VERIFYPEER+false&type=code)]</sup> tend to address the issue of missing SSL certificates on the client-side (`"SSL certificate problem: unable to get local issuer certificate"`) by setting `CURLOPT_VERIFYPEER` to `false`, [making the connection vulnerable to man-in-the-middle (MITM) attacks](https://curl.se/libcurl/c/CURLOPT_SSL_VERIFYPEER.html). This is an example PocketMine-MP plugin that showcases the usage of cURL with `CURLOPT_SSL_VERIFYPEER=true`.

To enable cURL with `CURLOPT_SSL_VERIFYPEER=true`, the plugin comes bundled with a Certificate Authority (CA) certificate obtained from https://curl.haxx.se/ca/cacert.pem.
The certificate is stored within the plugin's [`/resources`](https://github.com/Muqsit/CurlWithSSL/tree/master/plugin/resources) directory.
When performing a cURL request, the `CURLOPT_CAINFO` option is set to the path of the `cacert.pem` file on disk.

The example plugin makes a `GET` and a `POST` request to the [`/api/profile`](https://github.com/Muqsit/CurlWithSSL/blob/master/website/pages/api/profile.ts) endpoint on https://curlwithssl.pages.dev.
