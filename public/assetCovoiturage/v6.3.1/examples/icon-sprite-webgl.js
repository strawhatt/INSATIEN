(window.webpackJsonp=window.webpackJsonp||[]).push([[62],{297:function(e,t,a){"use strict";a.r(t);var n=a(3),r=a(2),i=a(5),o=a(54),s=a(24),l=a(26),p=a(11),c=a(4),d=a(169),u=new n.a({layers:[new i.a({source:new o.a({url:"https://api.tiles.mapbox.com/v4/mapbox.world-dark.json?secure&access_token=pk.eyJ1IjoidHNjaGF1YiIsImEiOiJjaW5zYW5lNHkxMTNmdWttM3JyOHZtMmNtIn0.CDIBD8H-G2Gf-cPkIuWtRg",crossOrigin:"anonymous"})})],target:document.getElementById("map"),view:new r.a({center:[0,4e6],zoom:2})}),f=new p.a({features:[],attributions:"National UFO Reporting Center"}),g={variables:{filterShape:"all"},filter:["case",["!=",["var","filterShape"],"all"],["==",["get","shape"],["var","filterShape"]],!0],symbol:{symbolType:"image",src:"data/ufo_shapes.png",size:16,color:["interpolate",["linear"],["get","year"],1950,[255,160,110],2013,[180,255,200]],rotateWithView:!1,offset:[0,0],textureCoord:["match",["get","shape"],"light",[0,0,.25,.5],"sphere",[.25,0,.5,.5],"circle",[.25,0,.5,.5],"disc",[.5,0,.75,.5],"oval",[.5,0,.75,.5],"triangle",[.75,0,1,.5],"fireball",[0,.5,.25,1],[.75,.5,1,1]]}},m={all:0},h=document.getElementById("shape-filter");h.addEventListener("input",(function(){g.variables.filterShape=h.options[h.selectedIndex].value,u.render()}));var v=new XMLHttpRequest;v.open("GET","data/csv/ufo_sighting_data.csv"),v.onload=function(){for(var e,t=v.responseText,a=[],n=t.indexOf("\n")+1;-1!=(e=t.indexOf("\n",n));){var r=t.substr(n,e-n).split(",");n=e+1;var i=Object(c.f)([parseFloat(r[5]),parseFloat(r[4])]);if(!isNaN(i[0])&&!isNaN(i[1])){var o=r[2];m[o]=(m[o]?m[o]:0)+1,m.all++,a.push(new s.a({datetime:r[0],year:parseInt(/[0-9]{4}/.exec(r[0])[0]),shape:o,duration:r[3],geometry:new l.a(i)}))}}f.addFeatures(a),Object.keys(m).sort((function(e,t){return m[t]-m[e]})).forEach((function(e){var t=document.createElement("option");t.text=e+" ("+m[e]+" sightings)",t.value=e,h.appendChild(t)}))},v.send(),u.addLayer(new d.a({source:f,style:g}));var w=document.getElementById("info");u.on("pointermove",(function(e){if(!u.getView().getInteracting()&&!u.getView().getAnimating()){var t=e.pixel;w.innerText="",u.forEachFeatureAtPixel(t,(function(e){var t=e.get("datetime"),a=e.get("duration"),n=e.get("shape");w.innerText="On "+t+", lasted "+a+' seconds and had a "'+n+'" shape.'}))}}))}},[[297,0]]]);
//# sourceMappingURL=icon-sprite-webgl.js.map