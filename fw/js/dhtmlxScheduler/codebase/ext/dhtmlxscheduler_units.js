scheduler._props={};scheduler.createUnitsView=function(A,E,D,B,C){if(typeof A=="object"){D=A.list;E=A.property;B=A.size||0;C=A.step||1;A=A.name}scheduler.date[A+"_start"]=scheduler.date.day_start;scheduler.templates[A+"_date"]=function(F){return scheduler.templates.day_date(F)};scheduler.templates[A+"_scale_date"]=function(G){var F=(scheduler._props[A].position||0)+Math.floor((scheduler._correct_shift(G.valueOf(),1)-scheduler._min_date.valueOf())/(60*60*24*1000));if(D[F].css){return"<span class='"+D[F].css+"'>"+D[F].label+"</span>"}else{return D[F].label}};scheduler.date["add_"+A]=function(F,G){return scheduler.date.add(F,G,"day")};scheduler.date["get_"+A+"_end"]=function(F){return scheduler.date.add(F,B||D.length,"day")};scheduler._props[A]={map_to:E,options:D,size:B,step:C,position:0};scheduler.attachEvent("onOptionsLoad",function(){var F=scheduler._props[A].order={};for(var G=0;G<D.length;G++){F[D[G].key]=G}if(scheduler._date){scheduler.setCurrentView(scheduler._date,scheduler._mode)}});scheduler.callEvent("onOptionsLoad",[])};scheduler.scrollUnit=function(A){var B=scheduler._props[this._mode];if(B){B.position=Math.min(Math.max(0,B.position+A),B.options.length-B.size);this.update_view()}};(function(){var D=function(L,J){if(L&&typeof J[L.map_to]=="undefined"){var I=scheduler;var H=24*60*60*1000;var K=Math.floor((J.end_date-I._min_date)/H);J.end_date=new Date(I.date.time_part(J.end_date)*1000+I._min_date.valueOf());J.start_date=new Date(I.date.time_part(J.start_date)*1000+I._min_date.valueOf());J[L.map_to]=L.options[K].key+L.position;return true}};var B=scheduler._reset_scale;var F=scheduler.is_visible_events;scheduler.is_visible_events=function(I){var H=F.apply(this,arguments);if(H){var K=scheduler._props[this._mode];if(K&&K.size){var J=K.order[I[K.map_to]];if(J<K.position||J>=K.size+K.position){return false}}}return H};scheduler._reset_scale=function(){var M=scheduler._props[this._mode];var H=B.apply(this,arguments);if(M){this._max_date=this.date.add(this._min_date,1,"day");var L=this._els.dhx_cal_data[0].childNodes;for(var I=0;I<L.length;I++){L[I].className=L[I].className.replace("_now","")}if(M.size&&M.size<M.options.length){var J=this._els.dhx_cal_header[0];var K=document.createElement("DIV");if(M.position){K.className="dhx_cal_prev_button";K.style.cssText="left:1px;top:2px;position:absolute;";K.innerHTML="&nbsp;";J.firstChild.appendChild(K);K.onclick=function(){scheduler.scrollUnit(M.step*-1)}}if(M.position+M.size<M.options.length){K=document.createElement("DIV");K.className="dhx_cal_next_button";K.style.cssText="left:auto; right:0px;top:2px;position:absolute;";K.innerHTML="&nbsp;";J.lastChild.appendChild(K);K.onclick=function(){scheduler.scrollUnit(M.step)}}}}return H};var C=scheduler._get_event_sday;scheduler._get_event_sday=function(H){var I=scheduler._props[this._mode];if(I){D(I,H);return I.order[H[I.map_to]]-I.position}return C.call(this,H)};var A=scheduler.locate_holder_day;scheduler.locate_holder_day=function(I,H,J){var K=scheduler._props[this._mode];if(K){D(K,J);return K.order[J[K.map_to]]*1+(H?1:0)-K.position}return A.apply(this,arguments)};var E=scheduler._mouse_coords;scheduler._mouse_coords=function(){var J=scheduler._props[this._mode];var I=E.apply(this,arguments);if(J){var H=this._drag_event;if(this._drag_id){H=this.getEvent(this._drag_id);this._drag_event.start_date=new Date()}H[J.map_to]=J.options[I.x+J.position].key;I.x=0}return I};var G=scheduler._time_order;scheduler._time_order=function(H){var I=scheduler._props[this._mode];if(I){H.sort(function(K,J){return I.order[K[I.map_to]]>I.order[J[I.map_to]]?1:-1})}else{G.apply(this,arguments)}};scheduler.attachEvent("onEventAdded",function(K,I){if(this._loading){return true}for(var H in scheduler._props){var J=scheduler._props[H];if(typeof I[J.map_to]=="undefined"){I[J.map_to]=J.options[0].key}}return true});scheduler.attachEvent("onEventCreated",function(K,H){var J=scheduler._props[this._mode];var I=this.getEvent(K);this._mouse_coords(H);D(J,I);this.event_updated(I);return true})})();