import { EditorContent } from '@tiptap/react'
import React, { useRef, useState } from 'react'

import { LinkMenu } from 'src/tiptap/components/menus'

import { useBlockEditor } from 'src/tiptap/hooks/useBlockEditor'

import '@/styles/index.css'

import { Sidebar } from 'src/tiptap/components/Sidebar'
import ImageBlockMenu from 'src/tiptap/extensions/ImageBlock/components/ImageBlockMenu'
import { ColumnsMenu } from 'src/tiptap/extensions/MultiColumn/menus'
import { TableColumnMenu, TableRowMenu } from 'src/tiptap/extensions/Table/menus'
import { EditorHeader } from './components/EditorHeader'
import { TextMenu } from '../menus/TextMenu'
import { ContentItemMenu } from '../menus/ContentItemMenu'
import { useSidebar } from 'src/tiptap/hooks/useSidebar'
import * as Y from 'yjs'
import { TiptapCollabProvider } from '@hocuspocus/provider'

export const BlockEditor = ({
  aiToken,
  ydoc,
  provider,
}: {
  aiToken?: string
  ydoc: Y.Doc | null
  provider?: TiptapCollabProvider | null | undefined
}) => {
  const [isEditable, setIsEditable] = useState(true)
  const menuContainerRef = useRef(null)

  const leftSidebar = useSidebar()
  const { editor, users } = useBlockEditor({
    aiToken,
    ydoc,
    provider,
    onTransaction({ editor: currentEditor }) {
      setIsEditable(currentEditor.isEditable)
    },
  })

  if (!editor || !users) {
    return null
  }

  console.log("BlockEditor", editor)

  return (
    <div className="flex h-full" ref={menuContainerRef}>
      <Sidebar isOpen={leftSidebar.isOpen} onClose={leftSidebar.close} editor={editor} />
      <div className="relative flex flex-col flex-1 h-full overflow-hidden">
        <EditorHeader
          editor={editor}
          isSidebarOpen={leftSidebar.isOpen}
          toggleSidebar={leftSidebar.toggle}
        />
        <EditorContent editor={editor} className="flex-1 overflow-y-auto" />
        <ContentItemMenu editor={editor} isEditable={isEditable} />
        <LinkMenu editor={editor} appendTo={menuContainerRef} />
        <TextMenu editor={editor} />
        <ColumnsMenu editor={editor} appendTo={menuContainerRef} />
        <TableRowMenu editor={editor} appendTo={menuContainerRef} />
        <TableColumnMenu editor={editor} appendTo={menuContainerRef} />
        <ImageBlockMenu editor={editor} appendTo={menuContainerRef} />
      </div>
    </div>
  )
}

export default BlockEditor
