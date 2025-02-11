
//import 'iframe-resizer/js/iframeResizer.contentWindow'

import { useSearchParams } from 'next/navigation'
import { useCallback, useEffect, useState } from 'react'

import { BlockEditor } from 'src/tiptap/components/BlockEditor'
import { createPortal } from 'react-dom'
import { Surface } from 'src/tiptap/components/ui/Surface'
import { Toolbar } from 'src/tiptap/components/ui/Toolbar'
import { Icon } from 'src/tiptap/components/ui/Icon'
import { useCollaboration } from 'src/tiptap/hooks/useCollaboration'

const useDarkmode = () => {
  const [isDarkMode, setIsDarkMode] = useState<boolean>(
    typeof window !== 'undefined' ? window.matchMedia('(prefers-color-scheme: dark)').matches : false,
  )

  useEffect(() => {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const handleChange = () => setIsDarkMode(mediaQuery.matches)
    mediaQuery.addEventListener('change', handleChange)

    return () => mediaQuery.removeEventListener('change', handleChange)
  }, [])

  useEffect(() => {
    document.documentElement.classList.toggle('dark', isDarkMode)
  }, [isDarkMode])

  const toggleDarkMode = useCallback(() => setIsDarkMode(isDark => !isDark), [])
  const lightMode = useCallback(() => setIsDarkMode(false), [])
  const darkMode = useCallback(() => setIsDarkMode(true), [])

  return {
    isDarkMode,
    toggleDarkMode,
    lightMode,
    darkMode,
  }
}

export default function Document({ params }: { params: { room: string } }) {
  const { isDarkMode, darkMode, lightMode } = useDarkmode()
  const searchParams = useSearchParams()
  const providerState = useCollaboration({
    docId: params.room,
    enabled: parseInt(searchParams?.get('noCollab') as string) !== 1,
  })


  if (providerState.state === 'loading') return

  const DarkModeSwitcher = createPortal(
    <Surface className="flex items-center gap-1 fixed bottom-6 right-6 z-[99999] p-1">
      <Toolbar.Button onClick={lightMode} active={!isDarkMode}>
        <Icon name="Sun" />
      </Toolbar.Button>
      <Toolbar.Button onClick={darkMode} active={isDarkMode}>
        <Icon name="Moon" />
      </Toolbar.Button>
    </Surface>,
    document.body,
  )

  return (
    <>
      {DarkModeSwitcher}
      <BlockEditor aiToken={""} ydoc={providerState.yDoc} provider={providerState.provider} />
    </>
  )
}
