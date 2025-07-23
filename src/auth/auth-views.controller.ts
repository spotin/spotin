import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { User } from '@/users/types/user';
import { Controller, Get, Query, Res } from '@nestjs/common';
import { ApiOperation, ApiOkResponse, ApiTags } from '@nestjs/swagger';
import { Response } from 'express';

@ApiTags('Views')
@Controller('auth')
export class AuthViewsController {
	@Get('login')
	@JwtOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the login page',
		description: 'Render the login page.',
		operationId: 'renderLogin',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	renderLogin(@AuthUser() user: User | undefined, @Res() res: Response): void {
		if (user) {
			return res.redirect('/');
		}

		return res.render('auth/login', {
			title: 'ui.auth.login.title',
			description: 'ui.auth.login.description',
		});
	}

	@Get('logout')
	@ApiOperation({
		summary: 'Render the logout page',
		description: 'Render the logout page.',
		operationId: 'renderLogout',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@JwtOrUnrestrictedAuth()
	renderLogout(@AuthUser() user: User | undefined, @Res() res: Response): void {
		if (!user?.id) {
			return res.redirect('/auth/login');
		}

		res.clearCookie('jwt');

		return res.render('auth/logout', {
			title: 'ui.auth.logout.title',
			description: 'ui.auth.logout.description',
		});
	}

	@Get('register')
	@ApiOperation({
		summary: 'Render the signup page',
		description: 'Render the signup page.',
		operationId: 'renderRegister',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@JwtOrUnrestrictedAuth()
	renderRegister(
		@AuthUser() user: User | undefined,
		@Res() res: Response,
	): void {
		if (user?.id) {
			return res.redirect('/');
		}

		return res.render('auth/register', {
			title: 'ui.auth.register.title',
			description: 'ui.auth.register.description',
		});
	}

	@Get('reset-password-request')
	@JwtOrUnrestrictedAuth()
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
	): void {
		if (user?.id) {
			return res.redirect('/');
		}

		return res.render('auth/reset-password-request', {
			title: 'ui.auth.resetPasswordRequest.title',
			description: 'ui.auth.resetPasswordRequest.description',
		});
	}

	@Get('reset-password')
	@JwtOrUnrestrictedAuth()
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
	): void {
		if (user?.id) {
			return res.redirect('/');
		}

		if (!token) {
			return res.redirect('/auth/reset-password-request');
		}

		return res.render('auth/reset-password', {
			title: 'ui.auth.resetPassword.title',
			description: 'ui.auth.resetPassword.description',
			token,
		});
	}
}
