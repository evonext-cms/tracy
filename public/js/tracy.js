(function(){var t=function(t,n){return function(){return t.apply(n,arguments)}};!function(){var n;return n=function(){function n(){this.dump=t(this.dump,this),this.format=t(this.format,this),this.sizeOf=t(this.sizeOf,this),this.typeOf=t(this.typeOf,this)}return n.prototype.typeOf=function(t){return null===t?"null":t instanceof Array?"array":typeof t},n.prototype.sizeOf=function(t){var n,r;r=0;for(n in t)t.hasOwnProperty(n)&&r++;return r},n.prototype.format=function(t,n,r){var a,s,p,e,c,u;switch(u=this.typeOf(t)){case"object":case"array":if(e="object"===u?this.sizeOf(t):t.length,0===e)return'<span class="tracy-dump-array">array</span> ()</span>\n';a=r>=n.depth-1?"tracy-collapsed":"",c="",c+='<span class="tracy-toggle '+a+'">',c+='<span class="tracy-dump-array">array</span> ('+e+")</span>\n",c+='<div class="'+a+'">';for(p in t)s=t[p],c+='<span class="tracy-dump-indent">   </span><span class="tracy-dump-key">'+p+"</span> =&gt; ",c+=this.format(s,n,r++);return c+="</div></span>";case"boolean":return c=t===!0?"TRUE":"FALSE",'<span class="tracy-dump-bool">'+c+"</span>\n";case"number":return'<span class="tracy-dump-number">'+t+"</span>\n";case"null":return'<span class="tracy-dump-null">NULL</span>\n';default:return'<span class="tracy-dump-string">"'+t+'"</span> ('+t.length+")\n"}},n.prototype.dump=function(t,n){return n||(n={}),n.depth||(n.depth=2),('&lt;pre class="tracy-dump"&gt;'+this.format(t,n,0)+"&lt;/pre&gt;").replace(/&lt;/g,"<").replace(/&gt;/g,">")},n}(),window.TracyDump=function(){var t;return t=new n,function(n,r){return t.dump(n,r)}}()}()}).call(this);