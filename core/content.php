<?php
/**
 * Eresus 2.10
 * 
 * �������������� ��������
 * 
 * ������� ���������� ��������� Eresus� 2
 * � 2004-2007, ProCreat Systems, http://procreat.ru/
 * � 2007, Eresus Group, http://eresus.ru/
 * 
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

class TContent {
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $db, $page, $plugins, $request;

    if (UserRights(EDITOR)) {
      $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
      $page->id = $item['id'];
      if (!array_key_exists($item['type'], $plugins->list)) {
        switch ($item['type']) {
          case 'default':
            $editor = new ContentPlugin;
            if (isset($request['arg']['update'])) $editor->update();
            else $result = $editor->adminRenderContent();
          break;
          case 'list': 
            if (isset($request['arg']['update'])) {
              $original = $item['content'];
              $item['content'] = $request['arg']['content'];
              $db->updateItem('pages', $item, "`id`='".$item['id']."'");
              sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br />'.$item['content']);
              goto($request['arg']['submitURL']);
            } else {
              $form = array(
                'name' => 'editURL',
                'caption' => admEdit,
                'width' => '100%',
                'fields' => array (
                  array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
                  array ('type' => 'html', 'name' => 'content', 'label' => admTemplListLabel, 'height' => '300px', 'value'=>isset($item['content'])?$item['content']:'$(items)'),
                ),
                'buttons' => array('apply', 'cancel'),
              );
              $result = $page->renderForm($form);
            }
          break;
          case 'url':
            if (isset($request['arg']['update'])) {
              $original = $item['content'];
              $item['content'] = $request['arg']['url'];
              $db->updateItem('pages', $item, "`id`='".$item['id']."'");
              sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br />'.$original.' &rarr; '.$item['content']);
              goto($request['arg']['submitURL']);
            } else {
              $form = array(
                'name' => 'editURL',
                'caption' => admEdit,
                'width' => '100%',
                'fields' => array (
                  array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
                  array ('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%', 'value'=>isset($item['content'])?$item['content']:''),
                ),
                'buttons' => array('apply', 'cancel'),
              );
              $result = $page->renderForm($form);
            }
          break;
          default:
          $result = $page->box(sprintf(errContentPluginNotFound, $item['type']), 'errorBox', errError);          
        }
      } else {
        $plugins->load($item['type']);
        $page->module = $plugins->items[$item['type']];
        $result = $plugins->items[$item['type']]->adminRenderContent();
      }
      return $result;
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>