<?php

namespace App\Http\Controllers;

use App\Models\UserPoint;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    private const PERIODS = [
        '7d'  => ['label' => 'Past 7 days',   'days' => 7],
        '21d' => ['label' => 'Past 21 days',  'days' => 21],
        '4w'  => ['label' => 'Past 4 weeks',  'days' => 28],
        '3m'  => ['label' => 'Past 3 months', 'days' => 90],
        '6m'  => ['label' => 'Past 6 months', 'days' => 180],
        'all' => ['label' => 'All time',      'days' => null],
    ];

    public function index(Request $request)
    {
        $period = $request->query('period', '4w');
        if (!array_key_exists($period, self::PERIODS)) $period = '4w';

        $config = self::PERIODS[$period];
        $query  = UserPoint::query()->selectRaw('user_id, SUM(points) as total_points')->groupBy('user_id');

        if ($config['days']) {
            $query->where('occurred_at', '>=', now()->subDays($config['days']));
        }

        $rows = $query->orderByDesc('total_points')->with('user')->get()
            ->filter(fn($r) => $r->user !== null)
            ->values();

        // Build breakdown for current user
        $myBreakdown = null;
        if (auth()->check()) {
            $bq = UserPoint::where('user_id', auth()->id())
                ->selectRaw('action, SUM(points) as pts, COUNT(*) as cnt')
                ->groupBy('action');
            if ($config['days']) {
                $bq->where('occurred_at', '>=', now()->subDays($config['days']));
            }
            $myBreakdown = $bq->get()->keyBy('action');
        }

        // Find current user rank
        $myRank = null;
        if (auth()->check()) {
            $myRank = $rows->search(fn($r) => $r->user_id === auth()->id());
            $myRank = $myRank !== false ? $myRank + 1 : null;
        }

        // Action labels for breakdown display
        $actionLabels = [
            'account_created'         => 'Joined Animal IQ',
            'post_published'          => 'Blog articles published',
            'research_published'      => 'Research projects published',
            'blog_view'               => 'Blog articles read',
            'blog_like'               => 'Blog likes given',
            'blog_bookmark'           => 'Blog bookmarks',
            'blog_comment'            => 'Blog comments',
            'blog_comment_like'       => 'Comment likes given',
            'post_received_view'      => 'Article views received',
            'post_received_like'      => 'Article likes received',
            'post_received_bookmark'  => 'Article bookmarks received',
            'post_received_comment'   => 'Article comments received',
            'forum_post'              => 'Forum posts created',
            'forum_like'              => 'Forum likes given',
            'forum_bookmark'          => 'Forum bookmarks',
            'forum_comment'           => 'Forum comments',
            'forum_comment_like'      => 'Forum comment likes',
            'forum_received_like'     => 'Forum likes received',
            'forum_received_bookmark' => 'Forum bookmarks received',
            'forum_received_comment'  => 'Forum comments received',
            'event_register'          => 'Events registered',
            'donation'                => 'Donations made',
            'share'                   => 'Content shared',
            'quiz_complete'           => 'Quizzes completed',
            'quiz_score'              => 'Quiz score bonuses',
            'quiz_high_score'         => 'Quiz high-score bonuses',
            'quiz_perfect'            => 'Perfect quiz scores',
        ];

        return view('public.leaderboard', compact('rows', 'period', 'myRank', 'myBreakdown', 'actionLabels'));
    }
}
