import {
	Controller,
	Post,
	Body,
	HttpCode,
	Res,
	ConflictException,
} from '@nestjs/common';
import {
	ApiBody,
	ApiConflictResponse,
	ApiOkResponse,
	ApiOperation,
	ApiTags,
} from '@nestjs/swagger';
import { AuthService } from '@/auth/auth.service';
import { LoginUserDto } from '@/auth/local/dtos/login-user.dto';
import { UsersService } from '@/users/users.service';
import { LocalAuth } from '@/auth/local/local-auth.decorator';
import { JwtDto } from '@/auth/jwt/dtos/jwt.dto';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { RegisterUserDto } from '@/auth/local/dtos/register-user.dto';
import { ResetPasswordAuth } from '@/auth/reset-password/reset-password.decorator';
import { ResetPasswordDto } from '@/auth/reset-password/dtos/reset-password.dto';
import { ResetPasswordRequestsService } from '@/reset-password-requests/reset-password-requests.service';
import { ResetPasswordRequestDto } from '@/auth/reset-password/dtos/reset-password-request.dto';
import { Response } from 'express';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';
import { User } from '@/users/types/user';
import { UserRole } from '@/users/enums/user-role';
import { I18n, I18nContext } from 'nestjs-i18n';

@ApiTags('Auth')
@Controller('api/auth')
export class AuthController {
	constructor(
		private readonly authService: AuthService,
		private readonly usersService: UsersService,
		private readonly resetPasswordRequestsService: ResetPasswordRequestsService,
	) {}

	@Post('login')
	@LocalAuth()
	@HttpCode(200)
	@ApiOperation({
		summary: 'Log in with email and password',
		description: 'Log in with email and password.',
		operationId: 'login',
	})
	@ApiBody({
		description: "The user's credentials.",
		type: LoginUserDto,
	})
	@ApiOkResponse({
		description: 'The user has been successfully logged in.',
		type: JwtDto,
	})
	async login(
		@AuthUser() user: User,
		@Res({ passthrough: true }) res: Response,
	): Promise<JwtDto> {
		const jwt = await this.authService.generateJwt(user);

		await this.resetPasswordRequestsService.deleteResetPasswordRequestForUser(
			user,
		);

		res.cookie('jwt', jwt.jwt, {
			httpOnly: true,
			sameSite: 'strict',
			secure: process.env.NODE_ENV === 'production',
		});

		return new JwtDto(jwt);
	}

	@Post('register')
	@HttpCode(200)
	@ApiOperation({
		summary: 'Register a new user',
		description: 'Register a new user.',
		operationId: 'register',
	})
	@ApiBody({
		description: "The user's details.",
		type: RegisterUserDto,
	})
	@ApiOkResponse({
		description: 'The user has been successfully signed up.',
	})
	@ApiConflictResponse({
		description: 'The username is already taken.',
	})
	async register(
		@Body() registerUserDto: RegisterUserDto,
		@I18n() i18n: I18nContext,
	): Promise<void> {
		try {
			await this.usersService.createUser(
				{
					...registerUserDto,
					enabled: true,
					role: UserRole.STANDARD_USER,
				},
				i18n.lang,
			);
		} catch (e) {
			if (e instanceof PrismaClientKnownRequestError && e.code === 'P2002') {
				const meta = e.meta as Record<string, Array<string>>;

				if (meta.target.includes('username')) {
					throw new ConflictException();
				}

				try {
					const user = await this.usersService.getUserByEmail(
						registerUserDto.email,
					);

					if (user.enabled) {
						await this.resetPasswordRequestsService.sendRecoverAccountForUser(
							user,
							i18n.lang,
						);
					}
				} catch (e) {
					// Do nothing
				}
			} else {
				throw e;
			}
		}
	}

	@Post('reset-password-request')
	@HttpCode(200)
	@ApiOperation({
		summary: 'Request password reset for user',
		description: 'Request password reset for user.',
		operationId: 'resetPasswordRequest',
	})
	@ApiBody({
		description: 'The email address of the user.',
		type: ResetPasswordRequestDto,
	})
	@ApiOkResponse({
		description:
			'The user password reset has been successfully requested. Note: if the email is inexistent, nothing will happen.',
	})
	async resetPasswordRequest(
		@Body() requestPasswordResetDto: ResetPasswordRequestDto,
		@I18n() i18n: I18nContext,
	): Promise<void> {
		try {
			const user = await this.usersService.getUserByEmail(
				requestPasswordResetDto.email,
			);

			if (user.enabled) {
				await this.resetPasswordRequestsService.sendResetPasswordRequestForUser(
					user,
					i18n.lang,
				);
			}
		} catch (e) {
			// Do nothing
		}
	}

	@Post('reset-password')
	@ResetPasswordAuth()
	@HttpCode(200)
	@ApiOperation({
		summary: 'Reset password for user',
		description: 'Reset password for user.',
		operationId: 'resetPassword',
	})
	@ApiBody({
		description: 'The new password.',
		type: ResetPasswordDto,
	})
	@ApiOkResponse({
		description: 'The user password has been successfully reset.',
	})
	async resetPassword(
		@AuthUser() user: User,
		@Body() resetPasswordDto: ResetPasswordDto,
	): Promise<void> {
		await this.resetPasswordRequestsService.deleteResetPasswordRequestForUser(
			user,
		);

		await this.usersService.updateUser(user.id, {
			password: resetPasswordDto.password,
		});
	}
}
