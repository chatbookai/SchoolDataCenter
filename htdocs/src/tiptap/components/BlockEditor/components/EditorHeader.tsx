'use client'

import { Icon } from 'src/tiptap/components/ui/Icon'
import { EditorInfo } from './EditorInfo'
import { Toolbar } from 'src/tiptap/components/ui/Toolbar'
import { Editor } from '@tiptap/core'
import { useEditorState } from '@tiptap/react'
import { useState, useCallback } from 'react'

export type EditorHeaderProps = {
  isSidebarOpen?: boolean
  toggleSidebar?: () => void
  editor: Editor
}

export const EditorHeader = ({ editor, isSidebarOpen, toggleSidebar }: EditorHeaderProps) => {
  const { characters, words } = useEditorState({
    editor,
    selector: (ctx): { characters: number; words: number } => {
      const { characters, words } = ctx.editor?.storage.characterCount || { characters: () => 0, words: () => 0 }
      return { characters: characters(), words: words() }
    },
  })

  const [content, setContent] = useState(editor.getJSON());

  editor.on('update', () => {
    const json = editor.getJSON();
    setContent(json);
  });

  const toggleEditable = useCallback(() => {
    editor.setOptions({ editable: !editor.isEditable })
    // force update the editor
    editor.view.dispatch(editor.view.state.tr)
  }, [editor])

  const saveEditorContent = () => {
    console.log("Updated content:", content);
  }

  return (
    <div className="flex flex-row items-center justify-between flex-none py-2 pl-6 pr-3 text-black bg-white border-b border-neutral-200 dark:bg-black dark:text-white dark:border-neutral-800">
      <div className="flex flex-row gap-x-1.5 items-center">
        <div className="flex items-center gap-x-1.5">
          <Toolbar.Button
            tooltip={isSidebarOpen ? 'Close sidebar' : 'Open sidebar'}
            onClick={toggleSidebar}
            active={isSidebarOpen}
            className={isSidebarOpen ? 'bg-transparent' : ''}
          >
            <Icon name={isSidebarOpen ? 'PanelLeftClose' : 'PanelLeft'} />
          </Toolbar.Button>
          <Toolbar.Button tooltip={editor.isEditable ? 'Disable editing' : 'Enable editing'} onClick={toggleEditable}>
            <Icon name={editor.isEditable ? 'PenOff' : 'Pen'} />
          </Toolbar.Button>
        </div>
      </div>
      <EditorInfo saveEditorContent={saveEditorContent} characters={characters} words={words} />
    </div>
  )
}
