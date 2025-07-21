import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { SessionOrUnrestrictedAuth } from '@/auth/session-or-unrestricted/session-or-unrestricted-auth.decorator';
import { SessionAuth } from '@/auth/session/session-auth.decorator';
import {
	EnvironmentVariables,
	SESSION_COOKIE_NAME,
} from '@/config/config.constants';
import { User } from '@/users/types/user';
import { Controller, Get, Query, Render, Req, Res } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { Request, Response } from 'express';

@ApiTags('Views')
@Controller('auth')
export class AuthViewsController {
	constructor(
		private readonly configService: ConfigService<EnvironmentVariables, true>,
	) {}

	@Get('login')
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the login page',
		description: 'Render the login page.',
		operationId: 'renderLogin',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderLogin(
		@AuthUser() user: User | undefined,
		@Res() res: Response,
	): void | Record<string, string> {
		if (user) {
			return res.redirect('/');
		}

		return res.render('auth/login', {
			title: 'Log in | Spot in',
		});
	}

	@Get('logout')
	@SessionAuth()
	@ApiOperation({
		summary: 'Render the logout page',
		description: 'Render the logout page.',
		operationId: 'renderLogout',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('auth/logout')
	renderLogout(
		@Req() req: Request,
		@Res({ passthrough: true }) res: Response,
	): void {
		const sessionName = this.configService.get(SESSION_COOKIE_NAME, {
			infer: true,
		});

		res.clearCookie(sessionName);

		req.session.destroy(() => ({
			title: 'Logout | Spot in',
		}));
	}

	@Get('register')
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the signup page',
		description: 'Render the signup page.',
		operationId: 'renderRegister',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderRegister(
		@AuthUser() user: User | undefined,
		@Res() res: Response,
	): void | Record<string, string> {
		if (user) {
			return res.redirect('/');
		}

		return res.render('auth/register', {
			title: 'Register | Spot in',
		});
	}

	@Get('reset-password-request')
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the reset password request page',
		description: 'Render the reset password request page.',
		operationId: 'renderResetPasswordRequest',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderResetPasswordRequest(
		@AuthUser() user: User | undefined,
		@Res() res: Response,
	): void | Record<string, string> {
		if (user) {
			return res.redirect('/');
		}

		return res.render('auth/reset-password-request', {
			title: 'Reset password request | Spot in',
		});
	}

	@Get('reset-password')
	@SessionOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the reset password page',
		description: 'Render the reset password page.',
		operationId: 'renderResetPassword',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderResetPassword(
		@AuthUser() user: User | undefined,
		@Res() res: Response,
		@Query('token') token: string,
	): void | Record<string, string> {
		if (user) {
			return res.redirect('/');
		}

		if (!token) {
			return res.redirect('/auth/reset-password-request');
		}

		return res.render('auth/reset-password', {
			title: 'Reset password | Spot in',
			token,
		});
	}
}
