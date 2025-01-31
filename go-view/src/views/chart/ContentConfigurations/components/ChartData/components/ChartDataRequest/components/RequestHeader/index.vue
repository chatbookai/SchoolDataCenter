<template>
  <n-space vertical>
    <div style="width: 600px">
      <n-tabs v-model:value="requestContentType" type="segment" size="small">
        <n-tab :name="RequestContentTypeEnum.DEFAULT" tab="普通请求"> </n-tab>
        <n-tab :name="RequestContentTypeEnum.SQL" tab="SQL 请求"> </n-tab>
      </n-tabs>
    </div>
    <div v-show="requestContentType === RequestContentTypeEnum.DEFAULT">
      <n-tabs type="line" animated v-model:value="tabValue">
        <n-tab v-for="item in RequestParamsTypeEnum" :key="item" :name="item" :tab="item"> {{ item }} </n-tab>
      </n-tabs>

      <!-- 各个页面 -->
      <div class="go-mt-3">
        <div v-if="tabValue !== RequestParamsTypeEnum.BODY">
          <request-header-table :target="requestParams[tabValue]" @update="updateRequestParams"></request-header-table>
        </div>

        <!-- 选择了 body -->
        <div v-else>
          <n-radio-group v-model:value="requestParamsBodyType" name="radiogroup">
            <n-space>
              <n-radio v-for="bodyEnum in RequestBodyEnumList" :key="bodyEnum" :value="bodyEnum">
                {{ bodyEnum }}
              </n-radio>
            </n-space>
          </n-radio-group>

          <!-- 为 none 时 -->
          <n-card class="go-mt-3 go-pb-3" v-if="requestParamsBodyType === RequestBodyEnum['NONE']">
            <n-text depth="3">该接口没有 Body 体</n-text>
          </n-card>

          <!-- 具有对象属性时 -->
          <template
            v-else-if="
              requestParamsBodyType === RequestBodyEnum['FORM_DATA'] ||
              requestParamsBodyType === RequestBodyEnum['X_WWW_FORM_URLENCODED']
            "
          >
            <request-header-table
              class="go-mt-3"
              :target="requestParams[RequestParamsTypeEnum.BODY][requestParamsBodyType]"
              @update="updateRequestBodyTable"
            ></request-header-table>
          </template>

          <!-- json  -->
          <template v-else-if="requestParamsBodyType === RequestBodyEnum['JSON']">
            <monaco-editor
              v-model:modelValue="requestParams[RequestParamsTypeEnum.BODY][requestParamsBodyType]"
              width="600px"
              height="200px"
              language="json"
            />
          </template>

          <!-- xml  -->
          <template v-else-if="requestParamsBodyType === RequestBodyEnum['XML']">
            <monaco-editor
              v-model:modelValue="requestParams[RequestParamsTypeEnum.BODY][requestParamsBodyType]"
              width="600px"
              height="200px"
              language="html"
            />
          </template>
        </div>
      </div>
    </div>
    <div v-show="requestContentType === RequestContentTypeEnum.SQL">
      <template v-if="requestHttpType === RequestHttpEnum.GET">
        <n-text>SQL 类型不支持 Get 请求，请使用其它方式</n-text>
      </template>
      <template v-else>
        <setting-item-box name="键名">  
          <n-tag type="primary" :bordered="false" style="width: 40px; font-size: 16px"> sql </n-tag>        
          <n-button type="primary" ghost @click="sendDbSourceHandle">
            获取远程数据源
          </n-button>
        </setting-item-box>

        <setting-item-box name="远程数据源">
          <n-select v-model:value="selectDbSource" filterable :options="remoteDbSource" clearable remote/>
          <n-select v-model:value="selectDbTable" filterable :options="remoteDbTable" clearable remote/>
        </setting-item-box>

        <setting-item-box name="数据表字段" style="width:100%;">
          <n-text style="width:100%;">{{selectDbTableFieldRef}}</n-text>
        </setting-item-box>

        <setting-item-box name="SQL语句">
          <monaco-editor v-model:modelValue="requestSQLContent['sql']" width="600px" height="200px" language="sql" />
        </setting-item-box>

      </template>
    </div>
  </n-space>
</template>

<script setup lang="ts">
import { ref, toRefs, PropType, toRaw, computed, watchEffect } from 'vue'
import { MonacoEditor } from '@/components/Pages/MonacoEditor'
import { RequestHeaderTable } from '../RequestHeaderTable/index'
import { SettingItemBox, SettingItem } from '@/components/Pages/ChartItemSetting'
import { useTargetData } from '@/views/chart/ContentConfigurations/components/hooks/useTargetData.hook'
import { RequestConfigType } from '@/store/modules/chartEditStore/chartEditStore.d'
import {
  RequestParamsTypeEnum,
  RequestContentTypeEnum,
  RequestParamsObjType,
  RequestBodyEnumList,
  RequestBodyEnum,
  RequestHttpEnum
} from '@/enums/httpEnum'
import { http, customizeHttp } from '@/api/http'
import { newFunctionHandle } from '@/utils'
import { SelectOption } from 'naive-ui'

const remoteDbSourceRef = ref<SelectOption[]>([])
const remoteDbSource = remoteDbSourceRef

const remoteDbTableRef = ref<SelectOption[]>([])
const remoteDbTable = remoteDbTableRef

const { targetData, chartEditStore } = useTargetData()

const loading = ref(false)
const controlModel = ref(false)
const showMatching = ref(false)

const selectDbSourceRef = ref(targetData.value.request.requestSQLContent['dbId'] ?? '')
const selectDbSource = selectDbSourceRef
const selectDbTableRef = ref(targetData.value.request.requestSQLContent['table'])
const selectDbTable = selectDbTableRef

const selectDbTableFieldRef = ref(targetData.value.request.requestSQLContent['field'])

function decodeBase64(str: string) {
  const decoder = new TextDecoder();
  const binaryString = atob(str);
  const bytes = new Uint8Array([...binaryString].map((char) => char.charCodeAt(0)));
  return decoder.decode(bytes);
}

const sendDbSourceHandle = async () => {
  if (!targetData.value?.request) {
    window.$message.warning('请选择一个公共接口！')
    return
  }
  loading.value = true
  try {
    console.log("chartEditStore.getRequestGlobalConfig", chartEditStore.getRequestGlobalConfig)
    //targetData.value.request.requestSQLContent['sql'] = '';
    targetData.value.request.requestSQLContent['action'] = 'dbsource';
    const res = await customizeHttp(toRaw(targetData.value.request), toRaw(chartEditStore.getRequestGlobalConfig))
    console.log("sendDbSourceHandle", res)
    loading.value = false
    if (res) {
      if (!res?.data) {
        window['$message'].warning('您的数据不符合默认格式，请配置过滤器！')
        showMatching.value = true
        return
      }
      remoteDbSourceRef.value = res?.data
      remoteDbTableRef.value = []
      selectDbSourceRef.value = ''
      //selectDbTableRef.value = ''
      //selectDbTableFieldRef.value = ''
      showMatching.value = true
      return
    }
    //window['$message'].warning('没有拿到返回值，请检查接口！')
  } catch (error) {
    console.error(error);
    loading.value = false
    window['$message'].warning('数据异常，请检查参数！')
  }
}

const sendDbSourceHandleOnly = async () => {
  loading.value = true
  try {
    targetData.value.request.requestSQLContent['action'] = 'dbsource';
    const res = await customizeHttp(toRaw(targetData.value.request), toRaw(chartEditStore.getRequestGlobalConfig))
    console.log("sendDbSourceHandle", res)
    loading.value = false
    if (res) {
      if (!res?.data) {
        window['$message'].warning('您的数据不符合默认格式，请配置过滤器！')
        showMatching.value = true
        return
      }
      remoteDbSourceRef.value = res?.data
      return
    }
  } catch (error) {
    console.error(error);
    loading.value = false
  }
}

const sendDbTableHandle = async () => {
  if (!targetData.value?.request) {
    window.$message.warning('请选择一个公共接口！')
    return
  }
  loading.value = true
  try {
    console.log("chartEditStore.getRequestGlobalConfig", chartEditStore.getRequestGlobalConfig)
    //targetData.value.request.requestSQLContent['sql'] = '';
    targetData.value.request.requestSQLContent['action'] = 'db';
    targetData.value.request.requestSQLContent['dbId'] = selectDbSource.value;
    const res = await customizeHttp(toRaw(targetData.value.request), toRaw(chartEditStore.getRequestGlobalConfig))
    console.log("sendDbTableHandle", res)
    loading.value = false
    if (res) {
      if (!res?.data) {
        window['$message'].warning('您的数据不符合默认格式，请配置过滤器！')
        showMatching.value = true
        return
      }
      const alldata = res?.data;
      remoteDbTableRef.value = [];
      alldata.map((item: string)=>{
        remoteDbTableRef.value.push({value: item, label: item})
      })
      showMatching.value = true
      return
    }
    //window['$message'].warning('没有拿到返回值，请检查接口！')
  } catch (error) {
    console.error(error);
    loading.value = false
    window['$message'].warning('数据异常，请检查参数！')
  }
}

const sendDbTableFieldHandle = async () => {
  if (!targetData.value?.request) {
    window.$message.warning('请选择一个公共接口！')
    return
  }
  loading.value = true
  try {
    console.log("chartEditStore.getRequestGlobalConfig", chartEditStore.getRequestGlobalConfig)
    //targetData.value.request.requestSQLContent['sql'] = '';
    targetData.value.request.requestSQLContent['action'] = 'table';
    targetData.value.request.requestSQLContent['dbId']    = selectDbSource.value;
    targetData.value.request.requestSQLContent['table'] = selectDbTable.value;
    const res = await customizeHttp(toRaw(targetData.value.request), toRaw(chartEditStore.getRequestGlobalConfig))
    console.log("sendDbTableHandle", res)
    loading.value = false
    if (res) {
      if (!res?.data) {
        window['$message'].warning('您的数据不符合默认格式，请配置过滤器！')
        showMatching.value = true
        return
      }
      const alldata = res?.data;
      selectDbTableFieldRef.value = alldata.join(',');
      targetData.value.request.requestSQLContent['field'] = alldata.join(',');
      showMatching.value = true
      return
    }
    //window['$message'].warning('没有拿到返回值，请检查接口！')
  } catch (error) {
    console.error(error);
    loading.value = false
    window['$message'].warning('数据异常，请检查参数！')
  }
}

watchEffect(() => {
  if (selectDbSource.value) {
    console.log("newDbSource", selectDbSource.value);
    sendDbSourceHandleOnly();
    sendDbTableHandle();
  }
  if (selectDbTable.value) {
    console.log("selectDbTable", selectDbTable.value);
    sendDbTableFieldHandle();
  }
});

const props = defineProps({
  targetDataRequest: Object as PropType<RequestConfigType>
})

const { requestHttpType, requestContentType, requestSQLContent, requestParams, requestParamsBodyType } = toRefs(
  props.targetDataRequest as RequestConfigType
)

const tabValue = ref<RequestParamsTypeEnum>(RequestParamsTypeEnum.PARAMS)

// 更新参数表格数据
const updateRequestParams = (paramsObj: RequestParamsObjType) => {
  if (tabValue.value !== RequestParamsTypeEnum.BODY) {
    requestParams.value[tabValue.value] = paramsObj
  }
}

// 更新参数表格数据
const updateRequestBodyTable = (paramsObj: RequestParamsObjType) => {
  if (
    tabValue.value === RequestParamsTypeEnum.BODY &&
    // 仅有两种类型有 body
    (requestParamsBodyType.value === RequestBodyEnum.FORM_DATA ||
      requestParamsBodyType.value === RequestBodyEnum.X_WWW_FORM_URLENCODED)
  ) {
    requestParams.value[RequestParamsTypeEnum.BODY][requestParamsBodyType.value] = paramsObj
  }
}
</script>

<style lang="scss" scoped>
.select-type {
  width: 300px;
}
</style>
