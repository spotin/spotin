import { Body, Controller, Get as NestGet, Param, Res } from '@nestjs/common';
import {
  ApiOperation,
  ApiOkResponse,
  ApiTags,
  ApiParam,
  ApiNotFoundResponse,
} from '@nestjs/swagger';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';
import { SpotsService } from './spots.service';
import { Response } from 'express';
import { ReadSpotDto } from './dtos/read-spot.dto';
import { AuthUser } from '../auth/decorators/auth-user.decorator';
import { Spot, User } from '@prisma/client';
import { JwtAuth } from '../auth/jwt/jwt-auth.decorator';
import { Post } from '../common/decorators/post.decorator';
import { GetOne } from 'src/common/decorators/get-one.decorator';
import { GetMany } from 'src/common/decorators/get-many.decorator';
import { Patch } from 'src/common/decorators/patch.decorator';
import { Delete } from 'src/common/decorators/delete.decorator';

@ApiTags('Spots')
@Controller('api/spots')
export class SpotsApiController {
  constructor(private readonly spotsService: SpotsService) {}

  @GetMany({
    name: 'Spots',
    summary: 'Get the spots',
    operationId: 'getSpotsApi',
    responseType: [ReadSpotDto],
  })
  @JwtAuth()
  async getSpotsApi(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @GetOne({
    name: 'Spot',
    summary: 'Get the specified spot',
    operationId: 'getSpotApi',
    responseType: ReadSpotDto,
  })
  @JwtAuth()
  async getSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id, user);

    return new ReadSpotDto(spot);
  }

  @Post({
    name: 'Spot',
    summary: 'Create a new spot',
    bodyType: CreateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'createSpotApi',
  })
  @JwtAuth()
  async createSpotApi(
    @AuthUser() user: User,
    @Body() createSpotDto: CreateSpotDto,
  ) {
    const newSpot = await this.spotsService.createSpot(createSpotDto, user);

    return new ReadSpotDto(newSpot);
  }

  @Patch({
    name: 'Spot',
    summary: 'Update the specified spot',
    bodyType: UpdateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'updateSpotApi',
  })
  @JwtAuth()
  async updateSpotApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    const updatedSpot = await this.spotsService.updateSpot(
      id,
      updateSpot,
      user,
    );

    return new ReadSpotDto(updatedSpot);
  }

  @Delete({
    name: 'Spot',
    summary: 'Delete the specified spot',
    operationId: 'deleteSpotApi',
  })
  @JwtAuth()
  async deleteSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);
  }

  @NestGet(':id/redirect')
  @ApiOperation({
    summary: 'Redirect to the link specified by the spot',
    description: 'Redirect to the link specified by the spot.',
    operationId: 'getSpotRedirection',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirection success.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotRedirection(@Res() res: Response, @Param('id') id: string) {
    const spot = (await this.spotsService.getSpot(id)) as Spot;

    if (!spot.configured) {
      res.redirect(`/spots/${id}/edit`);
    }

    if (spot?.redirection) {
      res.redirect(spot.redirection);
    }
  }

  @GetMany({
    path: 'public',
    name: 'Spots',
    summary: 'Get the public spots',
    operationId: 'getPublicSpotsApi',
    responseType: [ReadSpotDto],
  })
  async getPublicSpotsApi() {
    const spots = await this.spotsService.getPublicSpots();

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }
}
