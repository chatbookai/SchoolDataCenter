import { useRouter } from 'next/router'
import { Fragment, ReactNode, useState } from 'react'
import BlankLayout from 'src/@core/layouts/BlankLayout'
import { authConfig } from 'src/configs/auth'

import ModelMiddleSchoolSoulAssessment from 'src/views/Enginee/ModelMiddleSchoolSoulAssessment'


const ModelMiddleSchoolSoulAssessmentApp = () => {
  const [viewPageShareStatus, setViewPageShareStatus] = useState<boolean>(false)
  const handSetViewPageShareStatus = (NewStatus: boolean) => {
    setViewPageShareStatus(NewStatus)
  }

  const router = useRouter()
  const { id } = router.query
  const idList = String(atob(String(id))).split('____')
  if(idList[1] == '378' || idList[1] == '380' || idList[1] == '384' || idList[1] == '385') {
    const backEndApi = 'apps/apps_' + idList[1] + '.php'

    return <ModelMiddleSchoolSoulAssessment authConfig={authConfig} modelOriginal={"测评模式"} dataOriginal={null} id={String(idList[0])} backEndApi={backEndApi} viewPageShareStatus={viewPageShareStatus} handSetViewPageShareStatus={handSetViewPageShareStatus}/>

  }
  else {

    return <Fragment>Not Allow {idList[1]}</Fragment>
  }

}

ModelMiddleSchoolSoulAssessmentApp.getLayout = (page: ReactNode) => <BlankLayout>{page}</BlankLayout>

ModelMiddleSchoolSoulAssessmentApp.setConfig = () => {
  return {
    mode: 'light'
  }
}

export default ModelMiddleSchoolSoulAssessmentApp

