<?php function drawTicketSearchBar()
{ ?>
    <nav class="search-bar center">
        <div class="filter-condition round-border white-border">
            <label> Filter by </label>
            <select name="" class="filter-criteria" id="filter-ticket">
                <option value="title"> Title </option>
                <option value="creator"> Creator </option>
                <option value="replier"> Assigned agent </option>
                <option value="status"> Status </option>
                <option value="priority"> Priority </option>
                <option value="tag"> Hashtag </option>
                <option value="visibility"> Visibility </option>
                <option value="category"> Category </option>
            </select>
        </div>
        <div class="search-box center round-border white-border">
            <input id="search-ticket" type="text" placeholder="search">
            <img src="../images/icons/search.png">
        </div>

        <div class="order-condition round-border white-border">
            <label> Order by </label>
            <select name="" class="order-select" id="order-ticket">
                <option value="title"> Title </option>
                <option value="category"> Category </option>
                <option value="status"> Status </option>
                <option value="priority"> Priority </option>
                <option value="visibility"> Visibility </option>
                <option value="createDate"> Date </option>
            </select>
        </div>
    </nav>
<?php } ?>

<?php function drawTickets($tickets)
{
    if (!empty($tickets)) { ?>
        <section class = "tickets">
            <table class="tickets">
                <thead>
                    <tr class="ticket-info ">
                        <th>Creator</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Visibility</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </section>
        <div class="pagination-bar center"></div>
    <?php } else { ?>
        <img src="../images/icons/warning.png" class="warning-img">
        <h2 class="center">No tickets yet</h2>
    <?php }
}
?>

<?php function drawUserTickets(Session $session, User $user, array $tickets)
{ ?>
    <section id="user-tickets" class="center" data-user="<?= $user->name ?>">
        <header>
            <?= $user->name ?>'s tickets
        </header>
        <?php drawTickets($tickets);

        if ($session->getId() === $user->userId) { ?>
            <div class="button-wrap gradient round-border">
                <a href="../pages/create_ticket.php"><button>Create new ticket</button></a>
            </div>
        <?php } ?>
    </section>
<?php } ?>

<?php
function drawTicket($session,$ticket, $departments,$status,$priorities,$department,$messages, $history,$attachedFiles,$faqs)
{ ?>
    <section id="ticket-page" class="container" data-id="<?= $ticket->ticketId ?>" data-creator="<?= $ticket->creator->userId ?>" data-user="<?= $session->getId() ?>">
        <article id="tkt">
            <h1 class="highlight">
                <?= $ticket->title ?>
            </h1>
            <h3>Created by:
                <?= $ticket->creator->name ?> |
                <?= $ticket->date ?>
            </h3>
            <h2 id="ticket-text" class="round-border">
                <?= $ticket->text ?>
            </h2>
            <hr>
            <ol id="ticket-messages">
                <?php foreach ($messages as $message) {
                    if ($message['user']->userId !== $ticket->creator->userId) { ?>
                        <li class="replier-msg ticket-msg">
                        <?php } else { ?>
                        <li class="creator-msg ticket-msg">
                        <?php } ?>
                        <img src="<?= $message['user']->getPhoto() ?>" alt="user-img" class="circle-border">
                        <span>
                            <?= $message['user']->name ?>
                        </span>
                        <section class="message-content round-border">
                            <p>
                                <?= $message['text'] ?>
                            </p>
                            <p class="message-date">
                                <?= $message['date'] ?>
                            </p>
                        </section>
                    </li>
                <?php }
                if (($ticket->status == 'closed') && ($session->getId() === $ticket->creator->userId) && ($ticket->feedback === 1)) {
                    drawSurvey($ticket);
                } ?>
            </ol>
            <?php if (($session->isLoggedIn()) && ($ticket->status !== 'closed') && ($session->getId() === $ticket->creator->userId || $session->getId() === $ticket->replier->userId)) { ?>
                <section id="respond">
                    <textarea id="message-input" placeholder="Type your message..." rows="1"></textarea>
                    <button id="upload-button" class="no-background"><img src="../images/icons/upload.png" alt="Send"></button>
                    <?php if ($session->getId() === $ticket->replier->userId) {
                        drawFAQDropup($faqs);
                    } ?>
                    <button id="send-button" class="no-background"><img src="../images/icons/send.png" alt="Send"></button>
                </section>
            <?php } ?>
        </article>
        <?php drawSidebar($session, $ticket, $departments, $status, $priorities, $department, $history, $attachedFiles); ?>
    </section>
    <?php
}
?>


<?php function drawSidebar($session, $ticket, $departments, $status, $priorities, $department, $history, $attachedFiles){ ?>
    <aside class="sidebar">
        <article id="properties">
            <h2>Properties</h2>
            <ul class="center">
                <li> <label for="categories"> Category: 
                    <select name="categories" id="categories" <?= ((!$session->isLoggedIn()) || ($session->getRole() === 'client')) ? 'disabled' : ''?>>
                    <?php foreach($departments as $category) { ?>
                        <option value="<?=$category->category?>" <?=$category->category === $ticket->category ? 'selected' : ''?> ><?= $category->category ?></option>
                    <?php } ?>
                    </select>
                </label> </li>

                <li> <label for="stat">Status: 
                    <select name="stat" id="stat" <?= ((!$session->isLoggedIn()) || ($session->getRole() === 'client')) ? 'disabled' : ''?>>
                        <?php foreach($status as $stat) { ?>
                            <option value="<?= $stat?>" <?= $ticket->status === $stat ? 'selected' : '' ?>><?= $stat ?></option>
                        <?php } ?>
                    </select>
                </label> </li>
                    
                <li> <label for="priorities">Priority: 
                    <select name="priorities" id="priorities" <?= ((!$session->isLoggedIn()) || ($session->getRole() === 'client')) ? 'disabled' : ''?>>
                        <?php foreach($priorities as $priority) { ?>
                            <option value="<?= $priority?>" <?= $ticket->priority === $priority ? 'selected' : '' ?>><?= $priority ?> </option>
                        <?php } ?>
                    </select>
                </label> </li>
                    
                <li> <label for="assignee">Assignee: 
                    <select name="assignee" id="assignee" <?= ((!$session->isLoggedIn()) || ($session->getRole() === 'client')) ? 'disabled' : ''?>>
                        <?php if($ticket->status === 'new') { ?>
                            <option value="0" selected>Not assigned</option>
                        <?php } 
                        foreach($department->members as $member) { 
                            if ($member->userId !== $ticket->creator->userId) {?>
                            <option value="<?= $member->userId?>" <?= $ticket->replier->userId === $member->userId ? 'selected' : ''?>><?= $member->name ?> </option>
                        <?php } } ?>
                    </select>
                </label> </li>
                    
                <li> <label for="visibility">Visiblity: 
                    <select name="visibility" id="visibility" <?= ((!$session->isLoggedIn()) || ($session->getRole() === 'client')) ? 'disabled' : ''?>>
                        <option value="public" <?= $ticket->visibility === 'public' ? 'selected' : ''?>> Public  </option>
                        <option value="private" <?= $ticket->visibility === 'private' ? 'selected' : ''?>> Private  </option>
                    </select>
                </label> </li>
                    
                <li class="ticket-tags"> <label>Tags:
                    <?php if ($session->isLoggedIn() && $session->getRole() !== 'client') { ?>
                        <input type="text" id="tags-edit" name="tags-edit" list="taglist1">
                    <?php } ?>
                    <input type="hidden" id="ticket_tags" name="ticket_tags" />
                    <ul id="tag-container">
                        <?php foreach($ticket->tags as $tag){
                            if ($tag !== ' ') ?>
                            <li class="tag-block">
                                <span id="value"><?= $tag ?></span>
                                <?php if ($session->isLoggedIn() && $session->getRole() !== 'client') { ?>
                                    <button class="remove-button">x</button>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                    <datalist id="taglist1"></datalist>
                </label> </li>
            </ul>
            <?php if ($session->getRole() !== 'client') { ?>
                <div class="button-wrap gradient auth-button" id="edit-btn">
                    <button>Save changes</button>
                </div>
            <?php } ?>
        </article>

        <hr>
            
        <article id="history">
            <h2> History </h2>
            <ol class="timeline outer">
                <?php foreach ($history as $date => $changes) : ?>
                <li class="card-history">
                    <h3 class="title-history"><?= $date; ?></h3>
                    <div class="vert-flex"> <?= $changes[0]->user->name; ?>
                        <ul class="ticket-changes">
                            <?php foreach ($changes as $change) : ?>
                                <li>
                                <?php if ($change->old_field === '') { ?>
                                    <?= $change->changes ?>
                                <?php } else { ?>
                                    <?= $change->changes ?> : <?= $change->old_field; ?> >>> <?= $change->new_field;?>
                                <?php } ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li> <?php endforeach; ?>
            </ol>
        </article>
        
        <hr>
                
        <article id="files">
            <h2>Attached Files</h2>
            <ul>
                <?php foreach ($attachedFiles as $filename) { ?>
                <li class="center">
                    <a href="../files/ticket<?= $ticket->ticketId ?>_<?= $filename ?>" download><?= $filename ?></a>
                </li>
                <?php } ?>
            </ul>
        </article>
    </aside>
<?php } ?>

<?php function drawSurvey(Ticket $ticket)
{ ?>
    <li class="bot-msg ticket-msg">
        <img src="../images/icons/bot.png" alt="user-img" class="circle-border bot-img">
        <span> Satisfaction survey </span>
        <div class="message-content round-border">
            <p>Using your game expertise, how would you classify the level of awesomeness our agent's service
                achieved?</p>
            <section id="feedback" class="center" data-id="<?= $ticket->replier->userId ?>">
                <div class="vert-flex">
                    <button data-value=-10><img src="../images/icons/terrible.png" alt="Terrible"></button>
                    <p>Terrible</p>
                </div>
                <div class="vert-flex">
                    <button data-value=-5><img src="../images/icons/bad.png" alt="Not Good"></button>
                    <p>Not good</p>
                </div>
                <div class="vert-flex">
                    <button data-value=0><img src="../images/icons/normal.png" alt="Okay"></button>
                    <p>Okay</p>
                </div>
                <div class="vert-flex">
                    <button data-value=5><img src="../images/icons/great.png" alt="Great"></button>
                    <p>Great</p>
                </div>
                <div class="vert-flex">
                    <button data-value=10><img src="../images/icons/awesome.png" alt="Awesome"></button>
                    <p>Awesome</p>
                </div>
            </section>
    </li>
<?php } ?>

<?php function drawTicketForm($departments_get) { ?>
    <section class="vert-flex center">
        <div class="gradient round-border createTicket">
        <form action="../actions/ticket_actions/action_create_ticket.php" method="post" class="authentication-form">
            <label class="title">
                <h2>Title</h2>
                <h6>Be as specific and clear as possible </h6>
                <input type="text" name="title" required="required" placeholder="e.g. Selling item x gives more gold than its supposed to" maxlength="50">
            </label>

            <ul class="edit-sel">
                <li class="department-item">
                    <label class="departments-choice">
                        <h3>Department</h3>
                        <select name="category">
                            <?php foreach ($departments_get as $department) { ?>
                                <option value="<?= $department->category ?>"><?= $department->category ?></option>
                            <?php } ?>
                        </select>
                    </label>
                </li>
                <li class="visibility-item">
                    <label class="visibility">
                        <h3>Visibility</h3>
                        <select name="visibility" id="visibility">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </label>
                </li>
            </ul>


            <label>
                <h3 >Tags:</h3>
                <input type="text" id="tags" name="tags" list="taglist">
                <input type="hidden" id="chosen_tags" name="chosen_tags" />
                <div id="tag-container"></div>
                <datalist id="taglist"></datalist>
            </label>

            <div class="description">
                <h2> Description </h2>
                <h6> Tell us the details of your problem.</h6>
                <textarea id="description" name="text" required="required" rows="4" cols="40"></textarea>
            </div>    
            <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
            <div class="button-wrap gradient round-border"> <button type="submit">Create ticket</button> </div>
        </form>
    </div>
    </section>
    <?php
} ?>