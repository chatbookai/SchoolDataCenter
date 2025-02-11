import { TiptapCollabProvider } from '@hocuspocus/provider'
import { useEffect, useState } from 'react'
import { Doc as YDoc } from 'yjs'

function getProvider({ docId, token, yDoc }: { docId: string; token: string; yDoc: YDoc }) {
  return new TiptapCollabProvider({
    name: `${process.env.NEXT_PUBLIC_COLLAB_DOC_PREFIX}${docId}`,
    appId: process.env.NEXT_PUBLIC_TIPTAP_COLLAB_APP_ID ?? '',
    token: token,
    document: yDoc,
  })
}

export const useCollaboration = ({ docId, enabled = true }: { docId: string; enabled?: boolean }) => {
  const [provider, setProvider] = useState<
    | { state: 'loading' | 'idle'; provider: null; yDoc: null }
    | { state: 'loaded'; provider: TiptapCollabProvider; yDoc: YDoc }
  >(() => ({ state: enabled ? 'loading' : 'idle', provider: null, yDoc: null }))
  useEffect(() => {
    let isMounted = true
    setProvider({ state: 'idle', provider: null, yDoc: null })
    return () => {
      isMounted = false
    }
  }, [docId, enabled])

  return provider
}
