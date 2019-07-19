<script>

const schedule = <?php echo json_encode(getDoctorSchedule()); ?>;
const clinicData = schedule['clinicData'];
for(var pid of Object.keys(schedule)){
  if(schedule[pid].disabled === 'false'){
    schedule[pid].disabled = false
  }
}
</script>

<style>

.App {
  padding: 20px;
}
.ant-layout-content {
  padding: 20px;
}
.ant-upload-list-item-error, .ant-upload-list-item-done {
  display: none;
}

</style>

<script>

function saveSchedule(){
  $.ajax({
    url: "/wp-admin/admin-ajax.php",
    data: {
      action: 'putDoctorScheduleAjax',
      schedule: schedule
    },
    method: 'POST',
    success: function(s) {
      console.log('s', s)
    },
    error: function(e) {
      console.log('e', e)
    }
  }).done(function(data) {

  });
}

</script>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script><link href="/wp-content/plugins/doctors/static/css/2.7adcb32b.chunk.css" rel="stylesheet"><link href="/wp-content/plugins/doctors/static/css/main.60be52c8.chunk.css" rel="stylesheet"></head><body><noscript>You need to enable JavaScript to run this app.</noscript><div id="root"></div><script>!function(f){function e(e){for(var r,t,n=e[0],o=e[1],u=e[2],i=0,l=[];i<n.length;i++)t=n[i],a[t]&&l.push(a[t][0]),a[t]=0;for(r in o)Object.prototype.hasOwnProperty.call(o,r)&&(f[r]=o[r]);for(s&&s(e);l.length;)l.shift()();return c.push.apply(c,u||[]),p()}function p(){for(var e,r=0;r<c.length;r++){for(var t=c[r],n=!0,o=1;o<t.length;o++){var u=t[o];0!==a[u]&&(n=!1)}n&&(c.splice(r--,1),e=i(i.s=t[0]))}return e}var t={},a={1:0},c=[];function i(e){if(t[e])return t[e].exports;var r=t[e]={i:e,l:!1,exports:{}};return f[e].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.m=f,i.c=t,i.d=function(e,r,t){i.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(r,e){if(1&e&&(r=i(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var t=Object.create(null);if(i.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var n in r)i.d(t,n,function(e){return r[e]}.bind(null,n));return t},i.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(r,"a",r),r},i.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},i.p="/wp-content/plugins/doctors/";var r=window.webpackJsonp=window.webpackJsonp||[],n=r.push.bind(r);r.push=e,r=r.slice();for(var o=0;o<r.length;o++)e(r[o]);var s=n;p()}([])</script><script src="/wp-content/plugins/doctors/static/js/2.9faebbf5.chunk.js"></script><script src="/wp-content/plugins/doctors/static/js/main.69c41156.chunk.js"></script>