<div class="wrap">
    <h2>FetchTubeNG Settings</h2>
    <form method="post">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Title</th>
                <td><input type="text" name="FetchTubeNG_title" id="title" value="<? echo $settings['title'] ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Youtube API Key (see <a href="https://developers.google.com/youtube/registering_an_application" target="_blank">Google API Documentation</a>)</th>
                <td><input type="text" name="FetchTubeNG_apiKey" id="apiKey" value="<? echo $settings['apiKey'] ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Youtube Channel ID (e.g. UCgeTU1NGalsfrAdXHK8Mkxw)</th>
                <td><input type="text" name="FetchTubeNG_userId" id="userId" value="<? echo $settings['userId'] ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Type of Clips</th>
                <td>
                    <select name="FetchTubeNG_typeOf" id="typeOf">
                        <option <?php if($settings['typeOf'] == 'uploads') { echo 'selected'; } ?> value="uploads">Uploads</option>
                        <option <?php if($settings['typeOf'] == 'playlists') { echo 'selected'; } ?> value="playlists">Playlists</option>
                        <option <?php if($settings['typeOf'] == 'subscriptions') { echo 'selected'; } ?> value="subscriptions">Subscriptions</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Number of Clips</th>
                <td><input type="text" name="FetchTubeNG_numberOfClips" id=""numberOfClips" value="<? echo $settings['numberOfClips'] ?>"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Order By</th>
                <td>
                    <select name="FetchTubeNG_orderBy" id="sortBy">
                        <option <?php if($settings['orderBy'] == 'rating') { echo 'selected'; } ?> value="rating">Ratings</option>
                        <option <?php if($settings['orderBy'] == 'date') { echo 'selected'; } ?> value="date">Publish Date</option>
                        <option <?php if($settings['orderBy'] == 'relevance') { echo 'selected'; } ?> value="relevance">Relevance</option>
                        <option <?php if($settings['orderBy'] == 'viewcount') { echo 'selected'; } ?> value="viewCount">Views</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Thumbnail</th>
                <td><input type="text" name="FetchTubeNG_thumbWidth" id="thumbWidth" value="<? echo $settings['thumbWidth'] ?>" size="3" /> x
                    <input type="text" name="FetchTubeNG_thumbHeight" id="thumbHeight" value="<? echo $settings['thumbHeight'] ?>" size="3" /> Pixels</td>
            </tr>
            <tr valign="top">
                <th scope="row">Custom Error Message</th>
                <td><input type="text" name="FetchTubeNG_errorMsg" id="errorMsg" value="<? echo $settings['errorMsg'] ?>"/></td>
            </tr>
        </table>
		<div class="submit">
			<input type="submit" name="reset_FetchTubeNG_settings" id="resetBtn" value="<?php _e('Reset') ?>" />
			<input type="submit" name="save_FetchTubeNG_settings" id="submitBtn" value="<?php _e('Save') ?>" class="button-primary" />
		</div>
    </form>
</div>
