import { useRouter } from 'next/router'
import { authConfig } from 'src/configs/auth'

// ** Hooks
import UserList from 'src/views/Enginee/index'

const AppChat = () => {
  // ** States
  const backEndApi = "form_formfield.php"
  const router = useRouter()
  const _GET = router.query
  const FormId = String(_GET['FormId'])
  if (FormId != undefined) {
    return (
      <UserList authConfig={authConfig} backEndApi={backEndApi} externalId={FormId}/>
    )
  }
  else {
    return (
      <UserList authConfig={authConfig} backEndApi={backEndApi} externalId='0'/>
    )
  }

}


export default AppChat
