// Third-party Imports
import { configureStore } from '@reduxjs/toolkit'

// Slice Imports

export const store = configureStore({
  reducer: {
    
  },
  middleware: getDefaultMiddleware => getDefaultMiddleware({ serializableCheck: false })
})

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch
