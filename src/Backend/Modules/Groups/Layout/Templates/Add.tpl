{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabName">{$lblName|ucfirst}</a></li>
			<li><a href="#tabDashboard">{$lblDashboard|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
		</ul>

		<div id="tabName">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblName|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtName} {$txtNameError}
					</p>
				</div>
			</div>
		</div>

		<div id="tabDashboard">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblDashboard|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<label for="toggleChecksWidgets">{$lblDisplayWidgets|ucfirst}</label>
					<div class="dataGridHolder groupHolder">
						{option:widgets}
							{$widgets}
						{/option:widgets}
						{option:!widgets}
							{$msgNoWidgets|ucfirst}
						{/option:!widgets}
					</div>
				</div>
			</div>
		</div>

		<div id="tabPermissions">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblModules|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<label>{$lblSetPermissions|ucfirst}</label>
					<ul id="moduleList" class="inputList">
						{iteration:permissions}
							<li class="module">
								{$permissions.chk}
								<a href="#" class="icon iconCollapsed container" title="open">
									<span>
										<label for="{$permissions.id}">{$permissions.label}</label>
									</span>
								</a>
								<div class="datagridHolder hide clearfix">
									{$permissions.actions.dataGrid}
								</div>
							</li>
						{/iteration:permissions}
					</ul>
				</div>
			</div>
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblLanguages|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<label>{$lblSetPermissions|ucfirst}</label>
					<ul id="moduleList" class="inputList">
						{iteration:sites}
							<li class="module">
								{$sites.checkbox}
								<a href="#" class="icon iconCollapsed container" title="open">
									<span>
										<label for="site-{$sites.siteId}">{$sites.domain}</label>
									</span>
								</a>
								<div class="datagridHolder hide clearfix">
									<ul>
										{iteration:sites.languages}
											<li>
												<label for="{$sites.siteId}-{$sites.languages.language}">
													{$sites.languages.checkbox} {$sites.languages.language}
												</label>
											</li>
										{/iteration:sites.languages}
									</ul>
								</div>
							</li>
						{/iteration:sites}
					</ul>
				</div>
			</div>
		</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
