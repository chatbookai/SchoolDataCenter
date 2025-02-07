import { useRouter } from 'next/router'


// ** Hooks
import UserList from 'src/views/Enginee/index'
import { authConfig } from 'src/configs/auth'

const AppChat = () => {
  // ** States
  const backEndApi = "form_configsetting.php"
  const router = useRouter()
  const _GET = router.query
  const FlowId = String(_GET['FlowId'])
  if (FlowId != undefined) {
    return (
      <UserList authConfig={authConfig} backEndApi={backEndApi} externalId={FlowId}/>
    )
  }
  else {
    return (
      <UserList authConfig={authConfig} backEndApi={backEndApi} externalId='0'/>
    )
  }

}


export default AppChat
