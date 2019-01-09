/*! weixinwall 2017-07-06 */

function querystring(a){var b=location.search.match(new RegExp("[?&]"+a+"=([^&]+)","i"));return null==b||b.length<1?"":b[1]}function paserurl(a){var b=window.location.pathname,c=b.split("/");return c[2]=a,c.join("/")}function Path_url(a){return paserurl(a)}
//# sourceMappingURL=tool.js.map