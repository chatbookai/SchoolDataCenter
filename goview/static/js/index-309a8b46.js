var fe=Object.defineProperty;var ce=Object.getOwnPropertySymbols;var ve=Object.prototype.hasOwnProperty,he=Object.prototype.propertyIsEnumerable;var re=(h,i,y)=>i in h?fe(h,i,{enumerable:!0,configurable:!0,writable:!0,value:y}):h[i]=y,W=(h,i)=>{for(var y in i||(i={}))ve.call(i,y)&&re(h,y,i[y]);if(ce)for(var y of ce(i))he.call(i,y)&&re(h,y,i[y]);return h};var ae=(h,i,y)=>new Promise((q,w)=>{var $=C=>{try{D(y.next(C))}catch(T){w(T)}},A=C=>{try{D(y.throw(C))}catch(T){w(T)}},D=C=>C.done?q(C.value):Promise.resolve(C.value).then($,A);D((y=y.apply(h,i)).next())});import{d as Z,d4 as de,a1 as ie,r as n,o as s,D as U,w as e,b as t,br as oe,u as l,e as a,c as v,n as c,a6 as Q,F as k,q as I,t as x,Q as ge,U as ye,cL as be,aP as xe,h as se,aX as H,j as V,J as ue,s as pe,x as _e,aW as M}from"./index-3efa3bcd.js";import{S as Ee}from"./SettingItem-aacbe70b.js";import{S as Y}from"./SettingItemBox-94318b9b.js";import{g as we}from"./plugin-52ae8d1b.js";import{i as le}from"./icon-f6cbc454.js";import{d as Ce}from"./chartEditStore-1e6a587f.js";/* empty css                                                                      */import{n as Oe}from"./noData-e5cefb25.js";import{u as ee}from"./useTargetData.hook-4df2987a.js";import{M as me}from"./EditorWorker-1ca10225.js";import"./editorWorker-02e2e6ec.js";import{n as ke}from"./useLifeHandler.hook-22986a79.js";const Te={key:0,class:"no-data go-flex-center"},Se=["src"],De=Z({__name:"index",setup(h){const{CloseIcon:i,AddIcon:y,HelpOutlineIcon:q}=le.ionicons5,{targetData:w,chartEditStore:$}=ee(),A=[de.PARAMS,de.HEADER],D=ie(()=>{const f=w.value.interactActions;return f?f.map(p=>({label:p.interactName,value:p.interactType})):[]}),C=ie(()=>w.value.option),T=(f,p)=>{var O,g;if(!f)return{};const _=(O=$.requestGlobalConfig.requestDataPond.find(b=>b.dataPondId===f))==null?void 0:O.dataPondRequestConfig.requestParams;return _?_[p]:(g=$.componentList[$.fetchTargetIndex(f)])==null?void 0:g.request.requestParams[p]},N=f=>{if(!f||!w.value.interactActions)return[];const p=w.value.interactActions.find(_=>_.interactType===f);return(p==null?void 0:p.componentEmitEvents[C.value[be]])||[]},J=()=>{var b;const f=r=>r.reduce((d,u)=>(!u.groupList&&u.request.requestDataType===xe.AJAX&&u.request.requestUrl&&d.push(u),u.groupList&&u.groupList.length>0?[...d,...f(u.groupList)]:d),[]),_=f($.componentList).filter(r=>{const d=r.id!==w.value.id,u=r.chartConfig.chartFrame!==Ce.STATIC,L=!r.isGroup;return d&&u&&L}).map(r=>({id:r.id,title:r.chartConfig.title,disabled:!1,type:"componentList"})),g=$.requestGlobalConfig.requestDataPond.map(r=>({id:r.dataPondId,title:r.dataPondName,disabled:!1,type:"requestDataPond"})).concat(_);return(b=w.value.events.interactEvents)==null||b.forEach(r=>{g.forEach(d=>{d.id===r.interactComponentId&&(d.disabled=!0)})}),g},B=()=>{w.value.events.interactEvents.push({interactOn:void 0,interactComponentId:void 0,interactFn:{}})},K=f=>{we({message:"是否删除此关联交互模块?",onPositiveCallback:()=>{w.value.events.interactEvents.splice(f,1)}})};return(f,p)=>{const _=n("n-icon"),O=n("n-button"),g=n("n-text"),b=n("n-space"),r=n("n-divider"),d=n("n-tag"),u=n("n-select"),L=n("n-input-group"),F=n("n-tooltip"),R=n("n-table"),G=n("n-card"),z=n("n-collapse-item");return D.value.length?(s(),U(z,{key:0,title:"组件交互",name:"1"},{"header-extra":e(()=>[t(O,{type:"primary",tertiary:"",size:"small",onClick:oe(B,["stop"])},{icon:e(()=>[t(_,null,{default:e(()=>[t(l(y))]),_:1})]),default:e(()=>[a(" 新增 ")]),_:1},8,["onClick"])]),default:e(()=>[l(w).events.interactEvents.length?Q("",!0):(s(),v("div",Te,[c("img",{src:l(Oe),alt:"暂无数据"},null,8,Se),t(g,{depth:3},{default:e(()=>[a("暂无内容")]),_:1})])),(s(!0),v(k,null,I(l(w).events.interactEvents,(S,P)=>(s(),U(G,{key:P,class:"n-card-shallow",size:"small"},{default:e(()=>[t(b,{justify:"space-between"},{default:e(()=>[t(g,null,{default:e(()=>[a("关联组件 - "+x(P+1),1)]),_:2},1024),t(O,{type:"error",text:"",size:"small",onClick:E=>K(P)},{icon:e(()=>[t(_,null,{default:e(()=>[t(l(i))]),_:1})]),_:2},1032,["onClick"])]),_:2},1024),t(r,{style:{margin:"10px 0"}}),t(d,{bordered:!1,type:"primary"},{default:e(()=>[a(" 选择目标组件 ")]),_:1}),t(l(Y),{name:"触发事件",alone:!0},{default:e(()=>[D.value?(s(),U(L,{key:0},{default:e(()=>[t(u,{class:"select-type-options",value:S.interactOn,"onUpdate:value":E=>S.interactOn=E,size:"tiny",options:D.value},null,8,["value","onUpdate:value","options"])]),_:2},1024)):Q("",!0)]),_:2},1024),t(l(Y),{alone:!0},{name:e(()=>[t(g,null,{default:e(()=>[a("绑定")]),_:1}),t(F,{trigger:"hover"},{trigger:e(()=>[t(_,{size:"21",depth:3},{default:e(()=>[t(l(q))]),_:1})]),default:e(()=>[t(g,null,{default:e(()=>[a("不支持「静态组件」支持「组件」「公共APi」")]),_:1})]),_:1})]),default:e(()=>[t(u,{class:"select-type-options","value-field":"id","label-field":"title",size:"tiny",filterable:"",placeholder:"仅展示符合条件的组件",value:S.interactComponentId,"onUpdate:value":E=>S.interactComponentId=E,options:J()},null,8,["value","onUpdate:value","options"])]),_:2},1024),N(S.interactOn).length?(s(),U(l(Y),{key:0,name:"查询结果",alone:!0},{default:e(()=>[t(R,{size:"small",striped:""},{default:e(()=>[c("thead",null,[c("tr",null,[(s(),v(k,null,I(["参数","说明"],E=>c("th",{key:E},x(E),1)),64))])]),c("tbody",null,[(s(!0),v(k,null,I(N(S.interactOn),(E,m)=>(s(),v("tr",{key:m},[c("td",null,x(E.value),1),c("td",null,x(E.label),1)]))),128))])]),_:2},1024)]),_:2},1024)):Q("",!0),t(d,{bordered:!1,type:"primary"},{default:e(()=>[a(" 关联目标请求参数 ")]),_:1}),(s(),v(k,null,I(A,E=>t(l(Y),{name:E,key:E},{default:e(()=>[(s(!0),v(k,null,I(T(S.interactComponentId,E),(m,o,j)=>(s(),U(l(Ee),{key:j,name:`${o}`},{default:e(()=>[t(u,{size:"tiny",value:S.interactFn[o],"onUpdate:value":ne=>S.interactFn[o]=ne,options:N(S.interactOn),clearable:""},null,8,["value","onUpdate:value","options"])]),_:2},1032,["name"]))),128)),ge(t(g,{class:"go-pt-1",depth:"3"},{default:e(()=>[a(" 暂无数据 ")]),_:2},1536),[[ye,JSON.stringify(T(S.interactComponentId,E))==="{}"]])]),_:2},1032,["name"])),64))]),_:2},1024))),128))]),_:1})):Q("",!0)}}});const Ie=se(De,[["__scopeId","data-v-80f27dfa"]]),$e=`
console.log(e)
`,Ne=`
console.log(echarts)
`,Le=`
console.log(components)
`,Me=`
console.log(node_modules)
`,Ue=`
// 在渲染之后才能获取 dom 实例
e.el.addEventListener('click', () => {
  alert('我触发拉~');
}, false)
`,Ae=`
await import('https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/lodash.js/4.17.21/lodash.js')

// lodash 默认赋值给 "_"
console.log('isEqual', _.isEqual(['1'], ['1']))
`,Fe=`
// 获取echart实例
const chart = this.refs.vChartRef.chart

// 图表设置tooltip
chart.setOption({
  tooltip: {
    trigger: 'axis', //item
    enterable: true, 
    formatter (params) {
      return\`
        <div>
          <img src="https://portrait.gitee.com/uploads/avatars/user/1654/4964818_MTrun_1653229420.png!avatar30">
          <b><a href="https://gitee.com/dromara/go-view">《这是一个自定义的tooltip》</a></b>
        <div>
        <div style='border-radius:35px;color:#666'>
        \${Object.entries(params[0].value).map(kv => \`<div>\${kv[0]}:\${kv[1]}</div>\`).join('')}
        </div>
      \`;
    },
  }
})
`,Pe=`
// 组件样式作用域标识
const scoped = this.subTree.scopeId
function loadStyleString(css){
	let style = document.createElement('style')
	style.type = 'text/css'
	style.appendChild(document.createTextNode(css))
	let head = document.getElementsByTagName('head')[0]
	head.appendChild(style)
}
loadStyleString(\`
.dv-scroll-board[\${scoped}] {
  position: relative;
  overflow: hidden;
}
.dv-scroll-board[\${scoped}]::before {
  content: '';
  display: block;
  position: absolute;
  top: -20%;
  left: -100%;
  width: 550px;
  height: 60px;
  transform: rotate(-45deg);
  background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(255, 255, 255, 0.3), rgba(0, 0, 0, 0));
  animation: cross 2s infinite;
}
@keyframes cross{
  to{
    top: 80%;
    left: 100%;
    transform: rotate(-45deg);
  }
}
\`)
`,je=`
const chart = this.refs.vChartRef.chart
// 定义地图原点大小 同理可自定义标签等等内容
this.props.chartConfig.option.series[0].symbolSize = (val) => {
  return Math.sqrt(val[2]) / 3;
}
this.setupState.vEchartsSetOption();
let i = 0; // 当前轮播索引
const len = 3; // 轮播部分提示
(function showTips() {
  const action = (type, dataIndex) => {
    chart.dispatchAction({
      type,
      dataIndex,
      seriesIndex: 0,
    });
  }
  setInterval(() => {
    action("downplay", i);
    action("hideTip", i);
    if (i === len) i = 0;
    i++;
    action("highlight", i);
    action("showTip", i);
  }, 2000);
})()
`,Ve=[{description:"获取当前组件实例",code:$e},{description:"获取全局 echarts 实例",code:Ne},{description:"获取组件图表集合",code:Le},{description:"获取 nodeModules 实例",code:Me},{description:"获取远程 CDN 库",code:Ae},{description:"设置文字组件点击事件",code:Ue},{description:"修改图表 tooltip",code:Fe},{description:"添加【轮播列表】样式",code:Pe},{description:"修改【地图】圆点，新增提示自动轮播",code:je}],X=h=>(pe("data-v-d73dd522"),h=h(),_e(),h),qe={class:"func-annotate"},ze=X(()=>c("br",null,null,-1)),Be={class:"func-keyword"},Re={class:"go-ml-4"},Ge=X(()=>c("p",null,[a("}"),c("span",null,",")],-1)),We={class:"go-pl-3"},He=X(()=>c("span",{class:"func-keyword"},"async function   ",-1)),Je={class:"func-keyNameWord"},Ke=X(()=>c("p",{class:"go-pl-3 func-keyNameWord"},"}",-1)),Xe=X(()=>c("br",null,null,-1)),Qe=X(()=>c("br",null,null,-1)),Ye={class:"go-flex-items-center"},Ze=Z({__name:"index",setup(h){const{targetData:i,chartEditStore:y}=ee(),{DocumentTextIcon:q,ChevronDownIcon:w,PencilIcon:$}=le.ionicons5,A={[H.VNODE_BEFORE_MOUNT]:"渲染之前",[H.VNODE_MOUNTED]:"渲染之后"},D={[H.VNODE_BEFORE_MOUNT]:"此时组件 DOM 还未存在",[H.VNODE_MOUNTED]:"此时组件 DOM 已经存在"},C=V(!1),T=V(H.VNODE_MOUNTED);let N=V(W({},i.value.events.advancedEvents));const J=V(!1),B=()=>{let p="",_="",O="";return J.value=Object.entries(N.value).every(([g,b])=>{try{const r=Object.getPrototypeOf(function(){return ae(this,null,function*(){})}).constructor;return new r(b),!0}catch(r){return _=r.message,O=r.name,p=g,!1}}),{errorFn:p,message:_,name:O}},K=()=>{C.value=!1},f=()=>{if(B().errorFn){window.$message.error("事件函数错误，无法进行保存");return}Object.values(N.value).join("").trim()===""?i.value.events.advancedEvents={vnodeBeforeMount:void 0,vnodeMounted:void 0}:i.value.events.advancedEvents=W({},N.value),K()};return ue(()=>C.value,p=>{p&&(N.value=W({},i.value.events.advancedEvents))}),(p,_)=>{const O=n("n-icon"),g=n("n-button"),b=n("n-code"),r=n("n-card"),d=n("n-collapse-item"),u=n("n-text"),L=n("n-space"),F=n("n-tab-pane"),R=n("n-tabs"),G=n("n-layout"),z=n("n-collapse"),S=n("n-scrollbar"),P=n("n-tag"),E=n("n-layout-sider"),m=n("n-modal");return s(),v(k,null,[t(d,{title:"高级事件配置",name:"3"},{"header-extra":e(()=>[t(g,{type:"primary",tertiary:"",size:"small",onClick:_[0]||(_[0]=oe(o=>C.value=!0,["stop"]))},{icon:e(()=>[t(O,null,{default:e(()=>[t(l($))]),_:1})]),default:e(()=>[a(" 编辑 ")]),_:1})]),default:e(()=>[t(r,{class:"collapse-show-box"},{default:e(()=>[(s(!0),v(k,null,I(l(H),o=>(s(),v("div",{key:o},[c("p",null,[c("span",qe,"// "+x(A[o]),1),ze,c("span",Be,"async "+x(o),1),a(" (e, components, echarts, node_modules) { ")]),c("p",Re,[t(b,{code:(l(i).events.advancedEvents||{})[o]||"",language:"typescript"},null,8,["code"])]),Ge]))),128))]),_:1})]),_:1}),t(m,{class:"go-chart-data-monaco-editor",show:C.value,"onUpdate:show":_[2]||(_[2]=o=>C.value=o),"mask-closable":!1},{default:e(()=>[t(r,{bordered:!1,role:"dialog",size:"small","aria-modal":"true",style:{width:"1200px",height:"700px"}},{header:e(()=>[t(L,null,{default:e(()=>[t(u,null,{default:e(()=>[a("高级事件编辑器（配合源码使用）")]),_:1})]),_:1})]),"header-extra":e(()=>[]),action:e(()=>[t(L,{justify:"space-between"},{default:e(()=>[c("div",Ye,[t(P,{bordered:!1,type:"primary"},{icon:e(()=>[t(O,{component:l(q)},null,8,["component"])]),default:e(()=>[a(" 说明 ")]),_:1}),t(u,{class:"go-ml-2",depth:"2"},{default:e(()=>[a("通过提供的参数可为图表增加定制化的tooltip、交互事件等等")]),_:1})]),t(L,null,{default:e(()=>[t(g,{size:"medium",onClick:K},{default:e(()=>[a("取消")]),_:1}),t(g,{size:"medium",type:"primary",onClick:f},{default:e(()=>[a("保存")]),_:1})]),_:1})]),_:1})]),default:e(()=>[t(G,{"has-sider":"","sider-placement":"right"},{default:e(()=>[t(G,{style:{height:"580px","padding-right":"20px"}},{default:e(()=>[t(R,{value:T.value,"onUpdate:value":_[1]||(_[1]=o=>T.value=o),type:"card","tab-style":"min-width: 100px;"},{suffix:e(()=>[t(u,{class:"tab-tip",type:"warning"},{default:e(()=>[a("提示: "+x(D[T.value]),1)]),_:1})]),default:e(()=>[(s(!0),v(k,null,I(l(H),(o,j)=>(s(),U(F,{key:j,tab:`${A[o]}-${o}`,name:o},{default:e(()=>[c("p",We,[He,c("span",Je,x(o)+"(e, components, echarts, node_modules)  {",1)]),t(l(me),{modelValue:l(N)[o],"onUpdate:modelValue":ne=>l(N)[o]=ne,height:"480px",language:"javascript"},null,8,["modelValue","onUpdate:modelValue"]),Ke]),_:2},1032,["tab","name"]))),128))]),_:1},8,["value"])]),_:1}),t(E,{"collapsed-width":14,width:340,"show-trigger":"bar","collapse-mode":"transform","content-style":"padding: 12px 12px 0px 12px;margin-left: 3px;"},{default:e(()=>[t(R,{"default-value":"1","justify-content":"space-evenly",type:"segment"},{default:e(()=>[t(F,{tab:"验证结果",name:"1",size:"small"},{default:e(()=>[t(S,{trigger:"none",style:{"max-height":"505px"}},{default:e(()=>[t(z,{class:"go-px-3","arrow-placement":"right","default-expanded-names":[1,2,3]},{default:e(()=>[(s(!0),v(k,null,I([B()],o=>(s(),v(k,{key:o},[t(d,{title:"错误函数",name:1},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a(x(o.errorFn||"暂无"),1)]),_:2},1024)]),_:2},1024),t(d,{title:"错误信息",name:2},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a(x(o.name||"暂无"),1)]),_:2},1024)]),_:2},1024),t(d,{title:"堆栈信息",name:3},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a(x(o.message||"暂无"),1)]),_:2},1024)]),_:2},1024)],64))),128))]),_:1})]),_:1})]),_:1}),t(F,{tab:"变量说明",name:"2"},{default:e(()=>[t(S,{trigger:"none",style:{"max-height":"505px"}},{default:e(()=>[t(z,{class:"go-px-3","arrow-placement":"right","default-expanded-names":[1,2,3,4]},{default:e(()=>[t(d,{title:"e",name:1},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a("触发对应生命周期事件时接收的参数")]),_:1})]),_:1}),t(d,{title:"this",name:2},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a("图表组件实例")]),_:1}),Xe,(s(!0),v(k,null,I(["refs","setupState","ctx","props","..."],o=>(s(),U(P,{class:"go-m-1",key:o},{default:e(()=>[a(x(o),1)]),_:2},1024))),128))]),_:1}),t(d,{title:"components",name:3},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a("当前大屏内所有组件的集合id 图表组件中的配置id，可以获取其他图表组件进行控制")]),_:1}),t(b,{code:`{
  [id]: component
}`,language:"typescript"})]),_:1}),t(d,{title:"node_modules",name:4},{default:e(()=>[t(u,{depth:"3"},{default:e(()=>[a("以下是内置在代码环境中可用的包变量")]),_:1}),Qe,(s(!0),v(k,null,I(Object.keys(l(ke)||{}),o=>(s(),U(P,{class:"go-m-1",key:o},{default:e(()=>[a(x(o),1)]),_:2},1024))),128))]),_:1})]),_:1})]),_:1})]),_:1}),t(F,{tab:"介绍案例",name:"3"},{default:e(()=>[t(S,{trigger:"none",style:{"max-height":"505px"}},{default:e(()=>[t(z,{"arrow-placement":"right"},{default:e(()=>[(s(!0),v(k,null,I(l(Ve),(o,j)=>(s(),U(d,{key:j,title:`案例${j+1}：${o.description}`,name:j},{default:e(()=>[t(b,{code:o.code,language:"typescript"},null,8,["code"])]),_:2},1032,["title","name"]))),128))]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1},8,["show"])],64)}}});const et=se(Ze,[["__scopeId","data-v-d73dd522"]]),te=h=>(pe("data-v-d73b614e"),h=h(),_e(),h),tt={class:"func-annotate"},nt=te(()=>c("br",null,null,-1)),at={class:"func-keyword"},ot={class:"go-ml-4"},st=te(()=>c("p",null,[a("}"),c("span",null,",")],-1)),lt={class:"go-pl-3"},ct=te(()=>c("span",{class:"func-keyword"},"async function   ",-1)),rt={class:"func-keyNameWord"},dt=te(()=>c("p",{class:"go-pl-3 func-keyNameWord"},"}",-1)),it={class:"go-flex-items-center"},ut=Z({__name:"index",setup(h){const{targetData:i,chartEditStore:y}=ee(),{DocumentTextIcon:q,ChevronDownIcon:w,PencilIcon:$}=le.ionicons5,A={[M.ON_CLICK]:"单击",[M.ON_DBL_CLICK]:"双击",[M.ON_MOUSE_ENTER]:"鼠标进入",[M.ON_MOUSE_LEAVE]:"鼠标移出"},D=V(!1),C=V(M.ON_CLICK);let T=V(W({},i.value.events.baseEvent));const N=V(!1),J=()=>{let f="",p="",_="";return N.value=Object.entries(T.value).every(([O,g])=>{try{const b=Object.getPrototypeOf(function(){return ae(this,null,function*(){})}).constructor;return new b(g),!0}catch(b){return p=b.message,_=b.name,f=O,!1}}),{errorFn:f,message:p,name:_}},B=()=>{D.value=!1},K=()=>{if(J().errorFn){window.$message.error("事件函数错误，无法进行保存");return}Object.values(T.value).join("").trim()===""?i.value.events.baseEvent={[M.ON_CLICK]:void 0,[M.ON_DBL_CLICK]:void 0,[M.ON_MOUSE_ENTER]:void 0,[M.ON_MOUSE_LEAVE]:void 0}:i.value.events.baseEvent=W({},T.value),B()};return ue(()=>D.value,f=>{f&&(T.value=W({},i.value.events.baseEvent))}),(f,p)=>{const _=n("n-icon"),O=n("n-button"),g=n("n-code"),b=n("n-card"),r=n("n-collapse-item"),d=n("n-text"),u=n("n-space"),L=n("n-tab-pane"),F=n("n-tabs"),R=n("n-layout"),G=n("n-collapse"),z=n("n-scrollbar"),S=n("n-layout-sider"),P=n("n-tag"),E=n("n-modal");return s(),v(k,null,[t(r,{title:"基础事件配置",name:"2"},{"header-extra":e(()=>[t(O,{type:"primary",tertiary:"",size:"small",onClick:p[0]||(p[0]=oe(m=>D.value=!0,["stop"]))},{icon:e(()=>[t(_,null,{default:e(()=>[t(l($))]),_:1})]),default:e(()=>[a(" 编辑 ")]),_:1})]),default:e(()=>[t(b,{class:"collapse-show-box"},{default:e(()=>[(s(!0),v(k,null,I(l(M),m=>(s(),v("div",{key:m},[c("p",null,[c("span",tt,"// "+x(A[m]),1),nt,c("span",at,"async "+x(m),1),a(" (mouseEvent) { ")]),c("p",ot,[t(g,{code:(l(i).events.baseEvent||{})[m]||"",language:"typescript"},null,8,["code"])]),st]))),128))]),_:1})]),_:1}),t(E,{class:"go-chart-data-monaco-editor",show:D.value,"onUpdate:show":p[2]||(p[2]=m=>D.value=m),"mask-closable":!1},{default:e(()=>[t(b,{bordered:!1,role:"dialog",size:"small","aria-modal":"true",style:{width:"1200px",height:"700px"}},{header:e(()=>[t(u,null,{default:e(()=>[t(d,null,{default:e(()=>[a("基础事件编辑器")]),_:1})]),_:1})]),"header-extra":e(()=>[]),action:e(()=>[t(u,{justify:"space-between"},{default:e(()=>[c("div",it,[t(P,{bordered:!1,type:"primary"},{icon:e(()=>[t(_,{component:l(q)},null,8,["component"])]),default:e(()=>[a(" 说明 ")]),_:1}),t(d,{class:"go-ml-2",depth:"2"},{default:e(()=>[a("编写方式同正常 JavaScript 写法")]),_:1})]),t(u,null,{default:e(()=>[t(O,{size:"medium",onClick:B},{default:e(()=>[a("取消")]),_:1}),t(O,{size:"medium",type:"primary",onClick:K},{default:e(()=>[a("保存")]),_:1})]),_:1})]),_:1})]),default:e(()=>[t(R,{"has-sider":"","sider-placement":"right"},{default:e(()=>[t(R,{style:{height:"580px","padding-right":"20px"}},{default:e(()=>[t(F,{value:C.value,"onUpdate:value":p[1]||(p[1]=m=>C.value=m),type:"card","tab-style":"min-width: 100px;"},{suffix:e(()=>[t(d,{class:"tab-tip",type:"warning"},{default:e(()=>[a("提示: ECharts 组件会拦截鼠标事件")]),_:1})]),default:e(()=>[(s(!0),v(k,null,I(l(M),(m,o)=>(s(),U(L,{key:o,tab:`${A[m]}-${m}`,name:m},{default:e(()=>[c("p",lt,[ct,c("span",rt,x(m)+"(mouseEvent)  {",1)]),t(l(me),{modelValue:l(T)[m],"onUpdate:modelValue":j=>l(T)[m]=j,height:"480px",language:"javascript"},null,8,["modelValue","onUpdate:modelValue"]),dt]),_:2},1032,["tab","name"]))),128))]),_:1},8,["value"])]),_:1}),t(S,{"collapsed-width":14,width:340,"show-trigger":"bar","collapse-mode":"transform","content-style":"padding: 12px 12px 0px 12px;margin-left: 3px;"},{default:e(()=>[t(F,{"default-value":"1","justify-content":"space-evenly",type:"segment"},{default:e(()=>[t(L,{tab:"验证结果",name:"1",size:"small"},{default:e(()=>[t(z,{trigger:"none",style:{"max-height":"505px"}},{default:e(()=>[t(G,{class:"go-px-3","arrow-placement":"right","default-expanded-names":[1,2,3]},{default:e(()=>[(s(!0),v(k,null,I([J()],m=>(s(),v(k,{key:m},[t(r,{title:"错误函数",name:1},{default:e(()=>[t(d,{depth:"3"},{default:e(()=>[a(x(m.errorFn||"暂无"),1)]),_:2},1024)]),_:2},1024),t(r,{title:"错误信息",name:2},{default:e(()=>[t(d,{depth:"3"},{default:e(()=>[a(x(m.name||"暂无"),1)]),_:2},1024)]),_:2},1024),t(r,{title:"堆栈信息",name:3},{default:e(()=>[t(d,{depth:"3"},{default:e(()=>[a(x(m.message||"暂无"),1)]),_:2},1024)]),_:2},1024)],64))),128))]),_:1})]),_:1})]),_:1}),t(L,{tab:"变量说明",name:"2"},{default:e(()=>[t(z,{trigger:"none",style:{"max-height":"505px"}},{default:e(()=>[t(G,{class:"go-px-3","arrow-placement":"right","default-expanded-names":[1,2]},{default:e(()=>[t(r,{title:"mouseEvent",name:1},{default:e(()=>[t(d,{depth:"3"},{default:e(()=>[a("鼠标事件对象")]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1})]),_:1},8,["show"])],64)}}});const pt=se(ut,[["__scopeId","data-v-d73b614e"]]),kt=Z({__name:"index",setup(h){const{targetData:i}=ee();return V(!1),(y,q)=>{const w=n("n-text"),$=n("n-collapse");return s(),U($,{class:"go-mt-3","arrow-placement":"right","default-expanded-names":["1","2"]},{default:e(()=>[t(w,{depth:"3"},{default:e(()=>[a(" 组件 id： "),t(w,null,{default:e(()=>[a(x(l(i).id),1)]),_:1})]),_:1}),t(l(Ie)),t(l(pt)),t(l(et))]),_:1})}}});export{kt as default};
