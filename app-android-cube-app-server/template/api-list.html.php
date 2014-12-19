<div class="container">
    <div class="row mb-50">
        <h2>API list</h2>
        <p>除<code>api/init</code>接口外，<code>token, c, v, cv</code>参数每个接口都需要<p/>
        <ul>
            <li>
            <code>token</code>
            <span class="ml-30"><code>api/init</code>返回，标志用户身份</span>
            </li>
            <li>
            <code>v</code>: <span class="ml-30">版本号</span>
            <span class="ml-30">如:<code>1.0.1</code></span>
            </li>
            <li>
            <code>c</code>
            : <span class="ml-30">客户端类型</span>
            <span class="ml-30">如:<code>android/ios</code></span>
            </li>
            <li>
            <code>cv</code>: <span class="ml-30">客户端系统版本: android apilevel / ios 5/6/7...</span>
            </li>
        </ul>
        <hr/>

        <?php foreach ($page_data['list'] as $api => $item):?>
        <div class='row'>
            <div class='col-md-4'><h4><?php echo $api;?></h4></div>
        </div>
        <?php if ($item['des']): ?>
        <p class='ml-30'><?php $item->o('des'); ?></p>
        <?php endif;?>

        <?php if (count($item['params'])): ?>
        <p>parameter:</p>
        <ul>
            <?php foreach ($item['params'] as $key => $param):?>
            <li>
            <code><?php echo $key; ?></code>
            <?php if ($param['des']): ?>
            : <span class='ml-30'><?php $param->o('des');?></span>
            <?php endif;?>
            <span class='ml-30'>demo value:<code><?php $param->o('demo_vaule');?></code></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class='ml-30'><code>无参数</code></p>
        <?php endif;?>
        <p><a target='_blank' href='<?php $item->o('demo_url'); ?>'><?php $item->o('demo_url'); ?></a></p>
        <hr/>
        <?php endforeach; ?>
    </div>
</div>
