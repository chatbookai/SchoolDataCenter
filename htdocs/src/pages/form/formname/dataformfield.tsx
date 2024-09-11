import { useRouter } from 'next/router'


// ** Hooks
import UserList from 'src/views/Enginee/index'

const AppChat = () => {
  // ** States
  const backEndApi = "form_dataformfield.php"
  const router = useRouter()
  const _GET = router.query
  const FormId = String(_GET['FormId'])
  if (FormId != undefined) {
    return (
      <UserList backEndApi={backEndApi} externalId={FormId}/>
    )
  }
  else {
    return (
      <UserList backEndApi={backEndApi} externalId='0'/>
    )
  }

}


export default AppChat
