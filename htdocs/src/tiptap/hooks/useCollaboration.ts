import { TiptapCollabProvider } from '@hocuspocus/provider'
import { useEffect, useState } from 'react'
import { Doc as YDoc } from 'yjs'

export const useCollaboration = ({ docId, enabled = true }: { docId: string; enabled?: boolean }) => {
  const [provider, setProvider] = useState<
    | { state: 'loading' | 'idle'; provider: null; yDoc: null }
    | { state: 'loaded'; provider: TiptapCollabProvider; yDoc: YDoc }
  >(() => ({ state: enabled ? 'loading' : 'idle', provider: null, yDoc: null }))
  useEffect(() => {

    setProvider({ state: 'idle', provider: null, yDoc: null })

    return () => {

      //const isMounted = false
    }
  }, [docId, enabled])

  return provider
}
