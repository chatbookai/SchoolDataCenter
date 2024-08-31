import { useRouter } from 'next/router'

// ** Hooks
import UserList from '../../Enginee/index'

const AppChat = () => {
    // ** States
    const backEndApi = "form_formflow.php"
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
