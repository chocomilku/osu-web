// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import { SupportedChannelType, supportedChannelTypes } from 'interfaces/chat/channel-json';
import { observer } from 'mobx-react';
import core from 'osu-core-singleton';
import * as React from 'react';
import ConversationListItem from './conversation-list-item';
import JoinChannelButton from './join-channel-button';

function renderChannels(type: SupportedChannelType) {
  const channels = core.dataStore.channelStore.groupedChannels[type];
  if (channels.length === 0) return null;

  return (
    <React.Fragment key={type}>
      <div className='chat-conversation-list__header'>
        {osu.trans(`chat.channels.list.title.${type}`)}
      </div>
      {channels.map((channel) => <ConversationListItem key={channel.channelId} channel={channel} />)}
      <div className='chat-conversation-list-separator' />
    </React.Fragment>
  );
}

export default observer(() => (
  <div className='chat-conversation-list'>
    {supportedChannelTypes.map(renderChannels)}
    <JoinChannelButton />
  </div>
));
