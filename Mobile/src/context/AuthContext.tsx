// ** React Imports
import { createContext, useEffect, useState, ReactNode } from 'react'

// ** Types
import { AuthValuesType, UserDataType } from './types'

// ** Defaults
const defaultProvider: AuthValuesType = {
  user: null,
  loading: true,
  setUser: () => null,
  setLoading: () => Boolean,
  login: () => Promise.resolve(),
  logout: () => Promise.resolve()
}

const AuthContext = createContext(defaultProvider)

type Props = {
  children: ReactNode
}

const AuthProvider = ({ children }: Props) => {
  // ** States
  const [user, setUser] = useState<UserDataType | null>(defaultProvider.user)
  const [loading, setLoading] = useState<boolean>(defaultProvider.loading)

  const handleLogin = () => {
    console.log("handleLogin")
  }

  const handleLogout = () => {
    console.log("handleLogout")    
  }

  useEffect(() => {
    const user = {id: 1, role: 'admin', fullName: 'John Doe', username: 'johndoe', email: 'chivesweave@gmail.com'}
    setUser(user as UserDataType)
  }, [])

  const values = {
    user,
    loading,
    setUser,
    setLoading,
    login: handleLogin,
    logout: handleLogout
  }

  return <AuthContext.Provider value={values}>{children}</AuthContext.Provider>
}

export { AuthContext, AuthProvider }
